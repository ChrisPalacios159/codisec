<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <meta name="description" content="Información pública del Comité Distrital de Seguridad Ciudadana de Surquillo">
    <title>@yield('title', 'CODISEC Surquillo')</title>
    @vite(['resources/css/app.css', 'resources/js/app.js'])
</head>
<body class="min-h-screen antialiased">
    <div class="bg-cod-navy text-white/80"><div class="mx-auto flex max-w-7xl items-center justify-between px-4 py-2 text-xs sm:px-6 lg:px-8"><span>Municipalidad Distrital de Surquillo</span><a href="{{ url('/admin') }}" class="font-semibold text-white hover:text-cod-gold">Acceso de mantenimiento</a></div></div>
    <header class="sticky top-0 z-40 border-b border-slate-200 bg-white/95 shadow-sm backdrop-blur">
        <div class="mx-auto flex max-w-7xl items-center justify-between gap-6 px-4 py-4 sm:px-6 lg:px-8">
            <a href="{{ route('home') }}" class="flex items-center gap-3" aria-label="CODISEC Surquillo - Inicio"><span class="grid h-12 w-12 place-items-center rounded-xl bg-cod-blue text-lg font-black text-white shadow-md">CS</span><span><strong class="block text-xl tracking-tight text-cod-navy">CODISEC</strong><small class="block text-xs font-semibold uppercase tracking-[.17em] text-cod-teal">Surquillo seguro</small></span></a>
            <button data-menu-button class="rounded-lg border border-slate-300 p-2 text-cod-blue lg:hidden" aria-expanded="false" aria-label="Abrir menú"><svg viewBox="0 0 24 24" fill="none" stroke="currentColor" class="h-6 w-6"><path stroke-linecap="round" stroke-width="2" d="M4 6h16M4 12h16M4 18h16"/></svg></button>
            <nav class="hidden flex-wrap items-center justify-end gap-1 lg:flex" aria-label="Navegación principal">
                <a href="{{ route('home') }}" class="rounded-lg px-3 py-2 text-sm font-semibold text-slate-700 hover:bg-slate-100 hover:text-cod-teal">Inicio</a>
                @foreach($navigation as $item) @continue($item->url === '/' || mb_strtolower(trim($item->label)) === 'inicio')<a href="{{ $item->url }}" target="{{ $item->target }}" class="rounded-lg px-3 py-2 text-sm font-semibold text-slate-700 hover:bg-slate-100 hover:text-cod-teal">{{ $item->label }}</a>@endforeach
                <a href="{{ route('documents.index') }}" class="rounded-lg px-3 py-2 text-sm font-semibold text-slate-700 hover:bg-slate-100 hover:text-cod-teal">Documentos</a><a href="{{ route('contact.create') }}" class="ml-2 rounded-lg bg-cod-teal px-4 py-2 text-sm font-bold text-white hover:bg-cod-blue">Contacto</a>
            </nav>
        </div>
        <nav data-mobile-menu class="hidden border-t border-slate-200 bg-white px-4 py-3 lg:hidden"><a href="{{ route('home') }}" class="block py-2 font-semibold">Inicio</a>@foreach($navigation as $item) @continue($item->url === '/' || mb_strtolower(trim($item->label)) === 'inicio')<a href="{{ $item->url }}" target="{{ $item->target }}" class="block border-t border-slate-100 py-2 font-semibold">{{ $item->label }}</a>@endforeach<a href="{{ route('documents.index') }}" class="block border-t border-slate-100 py-2 font-semibold">Documentos</a><a href="{{ route('contact.create') }}" class="block border-t border-slate-100 py-2 font-semibold">Contacto</a></nav>
    </header>
    <main>@yield('content')</main>
    <footer class="mt-20 bg-cod-navy text-white"><div class="mx-auto grid max-w-7xl gap-10 px-4 py-12 sm:px-6 md:grid-cols-3 lg:px-8"><div><div class="text-2xl font-black">CODISEC Surquillo</div><p class="mt-3 text-sm leading-6 text-white/70">Información pública, planes, actas y actividades del Comité Distrital de Seguridad Ciudadana.</p></div><div><h2 class="font-bold text-cod-gold">Enlaces rápidos</h2><div class="mt-3 grid gap-2 text-sm text-white/75"><a href="{{ route('activities.index') }}">Actividades</a><a href="{{ route('sessions.index') }}">Sesiones</a><a href="{{ route('documents.index') }}">Documentos</a></div></div><div><h2 class="font-bold text-cod-gold">Transparencia</h2><p class="mt-3 text-sm leading-6 text-white/70">Los documentos históricos migrados conservan su referencia y fecha de publicación cuando estuvo disponible.</p></div></div><div class="border-t border-white/10 px-4 py-4 text-center text-xs text-white/55">© {{ date('Y') }} Municipalidad Distrital de Surquillo</div></footer>
</body>
</html>
