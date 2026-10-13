# Avance de migración CODISEC

Última actualización: 15 de septiembre de 2026 (America/Lima).

## Objetivo

Reemplazar el WordPress histórico de CODISEC por la aplicación Laravel ubicada en `C:\Users\Usuario\Desktop\Proyecto Laravel CODISEC`, conectar la aplicación con MySQL del servidor interno `10.10.20.233` y validar con pruebas automatizadas que la información se guarda y se publica correctamente.

## Estado comprobado

- Aplicación: Laravel 13.31.0 sobre PHP 8.3.33.
- Panel administrativo: Filament 5.8 en `/admin`.
- Base configurada: conexión `mariadb`, base `codisec_laravel`.
- Módulos implementados: páginas, categorías documentales, documentos, actividades, sesiones CODISEC, navegación, biblioteca de medios, mensajes de contacto, ajustes del sitio y usuarios.
- Importador WordPress disponible mediante `scripts/Import-WordPress.ps1` y `codisec:import-wordpress`.
- IIS preparado para servir exclusivamente desde `public/`.

## Diagnóstico de base de datos

El archivo `.env` continúa apuntando a `127.0.0.1:3306`. Inicialmente MariaDB local no estaba iniciado y Laravel devolvía `SQLSTATE[HY000] [2002]`.

Después de iniciar el entorno local:

- Laravel conectó correctamente a MariaDB local.
- Las 12 migraciones aparecen aplicadas.
- La aplicación usa tablas propias; no depende de WordPress durante la operación normal.

El puerto `3306` de `10.10.20.233` está accesible por red. Sin embargo, al probar Laravel contra ese host con las credenciales ya configuradas, MySQL respondió `SQLSTATE[HY000] [1130]`: el servidor no permite conexiones desde el equipo cliente actual. No se modificó el `.env` porque hacerlo dejaría la aplicación sin conexión.

## Pruebas ejecutadas

### PHPUnit / Laravel

Comando: `php artisan test --compact tests\Feature\PublicSiteTest.php`

Resultado: 6 pruebas aprobadas, 47 aserciones.

Se validaron las secciones públicas, almacenamiento del formulario de contacto, navegación sin duplicados, autorización del panel, módulos del tablero y encabezados de mantenimiento.

### Playwright

Comando: `scripts\Run-Playwright.ps1`

Resultado: 3 pruebas aprobadas en 2.2 minutos.

La suite verificó:

- Respuesta del sitio público y menú móvil.
- Los diez módulos del panel administrativo.
- Creación y edición de páginas.
- Creación de categorías y documentos en su sección correcta.
- Carga y publicación de archivos PDF e imágenes.
- Creación y publicación de actividades y sesiones.
- Creación de enlaces de navegación y medios.
- Edición de ajustes del sitio.
- Envío del formulario de contacto y lectura del mensaje en mantenimiento.
- Creación e ingreso de un usuario editor.
- Persistencia directa en la base mediante consultas Eloquent ejecutadas durante las pruebas.

Los datos E2E son temporales y la suite los elimina al terminar.

## Bloqueo actual

MySQL de `10.10.20.233` debe autorizar al usuario de la aplicación desde el host cliente indicado por el servidor, o la aplicación debe desplegarse en un equipo/origen ya autorizado. Se debe crear la base `codisec_laravel` en el servidor si todavía no existe y conceder únicamente los permisos necesarios sobre esa base.

No se deben guardar credenciales en este documento.

## Próximos pasos

1. En `10.10.20.233`, autorizar la conexión del usuario de Laravel desde el servidor donde quedará desplegada la aplicación.
2. Confirmar que existe la base `codisec_laravel` con `utf8mb4`.
3. Respaldar archivos y base antes de la migración definitiva.
4. Actualizar el `.env` del entorno de despliegue, limpiar la caché de configuración y ejecutar `php artisan migrate --force`.
5. Importar WordPress en modo de solo lectura y revisar conteos por módulo.
6. Ejecutar PHPUnit y Playwright contra el staging conectado a `10.10.20.233`.
7. Cambiar el binding de IIS únicamente después de validar staging y contar con aprobación expresa.

## Registro de cambios

- 2026-09-15: inspección inicial del proyecto y de sus servicios internos.
- 2026-09-15: verificada conectividad TCP con `10.10.20.233:3306`.
- 2026-09-15: detectada restricción de permisos MySQL para el host cliente.
- 2026-09-15: iniciada la base local y verificadas las 12 migraciones.
- 2026-09-15: PHPUnit aprobado (6 pruebas, 47 aserciones).
- 2026-09-15: Playwright aprobado (3 pruebas, 2.2 minutos).

## Validación remota del 15 de septiembre de 2026

- `.env` actualizado para usar `10.10.20.233`, conexión `mariadb`, base `codisec_laravel` y usuario VPN.
- Autenticación remota confirmada.
- Base inicialmente vacía: no existía la tabla de migraciones.
- Aplicadas correctamente las 12 migraciones en MySQL remoto.
- Playwright validó escrituras, ediciones, lecturas directas desde Eloquent, publicación y limpieza sobre MySQL remoto.
- Las dos pruebas administrativas y de persistencia pasaron; la prueba pública se repitió con su dato de navegación mínimo y también pasó.
- Verificación final: cero registros E2E o datos base artificiales en las tablas de contenido.
- La importación de WordPress sigue pendiente: el origen usa `DB_HOST=localhost` y, ejecutado desde este equipo, intenta acceder al MySQL local en lugar del servidor antiguo.
## Integración definitiva en `codisec` — 15 de septiembre de 2026

- Laravel quedó configurado con `DB_DATABASE=codisec` en `10.10.20.233`.
- Se confirmó que inicialmente no existían colisiones entre las tablas históricas `wp_*` y las tablas requeridas por Laravel.
- Se aplicaron las 12 migraciones Laravel dentro de `codisec`.
- Se convirtió el contenido histórico, conservando las tablas originales como fuente.
- Conteos convertidos: 11 páginas, 254 documentos, 167 sesiones, 94 actividades, 642 medios y 8 elementos de navegación.
- Playwright completo: 3 de 3 pruebas aprobadas en 2.3 minutos contra MySQL remoto.
- Verificación posterior: `wp_posts` conserva 1,116 registros y quedaron cero registros temporales E2E.
- `codisec_laravel` ya no es utilizada por la aplicación y no fue eliminada.
- El respaldo automático con el `mysqldump` antiguo de XAMPP no fue compatible con `caching_sha2_password`; se conservó como respaldo disponible el `script.sql` creado previamente por el usuario.
## Paquete de despliegue — 15 de septiembre de 2026

- Assets de producción compilados correctamente con Vite 8.3.
- Carpeta creada: `C:\Users\Usuario\Desktop\CODISEC-Laravel-Deploy-20260915`.
- Dependencias PHP de producción instaladas con PHP 8.3 y autoload optimizado.
- Entorno del paquete: `production`, depuración desactivada y conexión validada con `codisec`.
- Archivos esenciales verificados: `vendor/autoload.php`, `public/build/manifest.json`, `public/web.config` y `.env`.
- Tamaño aproximado: 505.84 MB, incluyendo documentos e imágenes históricas.
- Acceso SMB al servidor no disponible desde este equipo (`\\10.10.20.233\C$` y `net view` no accesibles); queda pendiente copiar la carpeta a una ruta del servidor y configurar IIS con document root apuntando a `public`.
- 2026-09-15: el paquete de despliegue fue movido a `C:\Users\Usuario\Desktop\Proyecto Laravel CODISEC\dist` y `/dist/` fue agregado a `.gitignore` para excluir archivos compilados y credenciales del repositorio.
- El runtime PHP quedó instalado de forma permanente en `C:\php83` en el servidor y ya no forma parte del paquete `dist`.
- El `dist` actual no contiene `.env`; conserva únicamente `.env.example`. La configuración real del servidor se mantiene al reemplazar archivos.

## Separación del panel administrativo — 15 de septiembre de 2026

- Se habilitó navegación SPA con precarga para reducir las recargas completas y mejorar la respuesta del panel.
- Se agregó un indicador global animado para cambios de pantalla y peticiones de Livewire, además de transiciones de entrada en tablas y tarjetas.
- Se crearon tablas administrativas independientes para Normas Legales, Evaluación de Desempeño, Plan Local de Seguridad Ciudadana, Boletines Estadísticos, Informes Trimestrales de Seguimiento e Informes Trimestrales de Cumplimiento.
- Cada módulo tiene URL, título, descripción, contador y listado propios. Los registros continúan almacenándose correctamente en la tabla `documents`, relacionados mediante `document_categories`, evitando duplicar información.
- Las acciones de creación conservan automáticamente la categoría del módulo, y las acciones de consulta y edición siguen usando el recurso canónico de documentos.
- Validación PHP: 8 pruebas aprobadas con 103 aserciones.
- Validación Playwright: 3 pruebas aprobadas en navegador, incluyendo escritura, actualización, publicación, archivos y limpieza de datos temporales.
- Assets de producción recompilados con Vite y sincronizados en `dist`, sin copiar `.env`.
