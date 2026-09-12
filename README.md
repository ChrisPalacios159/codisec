# CODISEC Surquillo — Laravel

Reemplazo independiente del WordPress histórico de CODISEC. Incluye sitio público, panel de mantenimiento con Filament y un importador reproducible de solo lectura.

## Tecnología

- Laravel 13.31 y PHP 8.3.33.
- Filament 5.8 para `/admin`.
- MariaDB/MySQL, Blade, Tailwind CSS 4 y Vite.
- Preparado para IIS mediante `public/web.config`.

## Accesos locales

- Sitio público: `http://127.0.0.1:8000`
- Panel: `http://127.0.0.1:8000/admin`
- Las credenciales iniciales están en `storage/app/private/CREDENCIALES_MANTENIMIENTO.txt`. Ese directorio está excluido de Git. Cambie la contraseña después del primer ingreso y elimine el archivo.

## Arranque

```powershell
.\scripts\Start-Development.ps1
```

## Importación desde WordPress

El script lee `wp_*`, nunca escribe en esa base, copia los archivos y limpia únicamente las tablas de contenido de la base nueva cuando se usa `--fresh`.

```powershell
.\scripts\Import-WordPress.ps1
```

## Operación

```powershell
C:\Users\Usuario\Tools\php-8.3.33\php.exe artisan migrate --force
npm install
npm run build
.\scripts\Export-Database.ps1
```

El document root de IIS debe apuntar a `public`, nunca a la raíz del proyecto. El cambio de binding del WordPress de producción no forma parte del staging y requiere respaldo, pruebas y aprobación expresa.

## Modelo migrado

Páginas, categorías, documentos, sesiones, actividades, navegación, medios, mensajes de contacto, ajustes y usuarios de mantenimiento. Se omiten revisiones de WordPress y la página de ejemplo.
