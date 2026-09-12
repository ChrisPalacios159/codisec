"""Small HTTPS CONNECT proxy using Google DNS directly.

Used only to make dependency installation reproducible on this Windows host,
whose configured DNS server intermittently fails for Packagist and GitHub.
"""

from __future__ import annotations

import random
import select
import socket
import socketserver
import struct
import threading
import time


DNS_SERVER = ("8.8.8.8", 53)
CACHE: dict[str, tuple[float, str]] = {}
CACHE_LOCK = threading.Lock()


def _skip_name(packet: bytes, offset: int) -> int:
    while True:
        length = packet[offset]
        if length == 0:
            return offset + 1
        if length & 0xC0 == 0xC0:
            return offset + 2
        offset += length + 1


def resolve_ipv4(host: str) -> str:
    try:
        socket.inet_aton(host)
        return host
    except OSError:
        pass

    with CACHE_LOCK:
        cached = CACHE.get(host)
        if cached and cached[0] > time.time():
            return cached[1]

    query_id = random.randint(0, 65535)
    labels = b"".join(bytes([len(part)]) + part.encode("idna") for part in host.split(".")) + b"\0"
    query = struct.pack("!HHHHHH", query_id, 0x0100, 1, 0, 0, 0) + labels + struct.pack("!HH", 1, 1)

    with socket.socket(socket.AF_INET, socket.SOCK_DGRAM) as dns:
        dns.settimeout(5)
        dns.sendto(query, DNS_SERVER)
        packet, _ = dns.recvfrom(4096)

    response_id, flags, questions, answers, _, _ = struct.unpack("!HHHHHH", packet[:12])
    if response_id != query_id or flags & 0x000F:
        raise OSError(f"DNS lookup failed for {host}")

    offset = 12
    for _ in range(questions):
        offset = _skip_name(packet, offset) + 4

    for _ in range(answers):
        offset = _skip_name(packet, offset)
        record_type, record_class, ttl, data_length = struct.unpack("!HHIH", packet[offset : offset + 10])
        offset += 10
        data = packet[offset : offset + data_length]
        offset += data_length
        if record_type == 1 and record_class == 1 and data_length == 4:
            address = socket.inet_ntoa(data)
            with CACHE_LOCK:
                CACHE[host] = (time.time() + max(30, ttl), address)
            return address

    raise OSError(f"No IPv4 address found for {host}")


class ConnectHandler(socketserver.StreamRequestHandler):
    timeout = 30

    def handle(self) -> None:
        request_line = self.rfile.readline(8192).decode("ascii", "replace").strip()
        while self.rfile.readline(8192) not in (b"\r\n", b"\n", b""):
            pass

        parts = request_line.split()
        if len(parts) != 3 or parts[0].upper() != "CONNECT":
            self.wfile.write(b"HTTP/1.1 405 Method Not Allowed\r\nConnection: close\r\n\r\n")
            return

        host, port_text = parts[1].rsplit(":", 1)
        remote = socket.create_connection((resolve_ipv4(host), int(port_text)), timeout=30)
        try:
            self.wfile.write(b"HTTP/1.1 200 Connection Established\r\n\r\n")
            self.wfile.flush()
            sockets = [self.connection, remote]
            while True:
                readable, _, exceptional = select.select(sockets, [], sockets, 60)
                if exceptional or not readable:
                    return
                for source in readable:
                    try:
                        data = source.recv(65536)
                    except OSError:
                        return
                    if not data:
                        return
                    try:
                        (remote if source is self.connection else self.connection).sendall(data)
                    except OSError:
                        return
        finally:
            remote.close()


class ThreadingProxy(socketserver.ThreadingTCPServer):
    allow_reuse_address = True
    daemon_threads = True


if __name__ == "__main__":
    with ThreadingProxy(("127.0.0.1", 18080), ConnectHandler) as server:
        server.serve_forever()
