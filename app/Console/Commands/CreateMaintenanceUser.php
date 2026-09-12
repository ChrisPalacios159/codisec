<?php

namespace App\Console\Commands;

use App\Models\User;
use Illuminate\Console\Attributes\Description;
use Illuminate\Console\Attributes\Signature;
use Illuminate\Console\Command;
use Illuminate\Support\Facades\File;
use Illuminate\Support\Facades\Hash;

#[Signature('codisec:create-maintenance-user {--name=Administrador CODISEC} {--email=mantenimiento@codisec.local} {--password=} {--credentials-file=} ')]
#[Description('Crea o actualiza el usuario autorizado para el panel de mantenimiento')]
class CreateMaintenanceUser extends Command
{
    public function handle(): int
    {
        $password = (string) ($this->option('password') ?: $this->randomPassword());
        $email = mb_strtolower(trim((string) $this->option('email')));
        User::updateOrCreate(
            ['email' => $email],
            ['name' => (string) $this->option('name'), 'password' => Hash::make($password), 'role' => 'administrator', 'is_active' => true, 'email_verified_at' => now()]
        );
        if ($path = $this->option('credentials-file')) {
            File::ensureDirectoryExists(dirname((string) $path));
            File::put((string) $path, 'Panel: '.url('/admin').PHP_EOL."Usuario: {$email}".PHP_EOL."Contraseña temporal: {$password}".PHP_EOL.'Creado: '.now()->toDateTimeString().PHP_EOL);
            $this->info('Usuario creado. Las credenciales temporales se guardaron en la ruta indicada.');
        } else {
            $this->warn('Contraseña temporal: '.$password);
        }

        return self::SUCCESS;
    }

    private function randomPassword(): string
    {
        return 'C0d!'.substr(strtr(base64_encode(random_bytes(18)), '+/', 'AZ'), 0, 22);
    }
}
