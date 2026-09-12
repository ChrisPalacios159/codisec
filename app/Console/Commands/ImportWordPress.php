<?php

namespace App\Console\Commands;

use App\Models\Activity;
use App\Models\CodisecSession;
use App\Models\Document;
use App\Models\DocumentCategory;
use App\Models\MediaAsset;
use App\Models\NavigationItem;
use App\Models\Page;
use App\Models\SiteSetting;
use Carbon\Carbon;
use Illuminate\Console\Attributes\Description;
use Illuminate\Console\Attributes\Signature;
use Illuminate\Console\Command;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\File;
use Illuminate\Support\Str;
use Throwable;

#[Signature('codisec:import-wordpress {--uploads-path= : Ruta de wp-content/uploads que se copiará} {--fresh : Vacía solamente las tablas de destino antes de importar}')]
#[Description('Importa CODISEC desde WordPress mediante una conexión de solo lectura')]
class ImportWordPress extends Command
{
    private string $prefix;

    public function handle(): int
    {
        $this->prefix = (string) env('WORDPRESS_DB_PREFIX', 'wp_');

        if (! preg_match('/^[A-Za-z0-9_]+$/', $this->prefix)) {
            $this->error('WORDPRESS_DB_PREFIX no es válido.');

            return self::FAILURE;
        }

        foreach (['WORDPRESS_DB_HOST', 'WORDPRESS_DB_DATABASE', 'WORDPRESS_DB_USERNAME'] as $required) {
            if (! env($required)) {
                $this->error("Falta la variable {$required}.");

                return self::FAILURE;
            }
        }

        try {
            DB::connection('wordpress')->select('SELECT 1');
        } catch (Throwable $exception) {
            $this->error('No se pudo abrir la conexión de lectura a WordPress: '.$exception->getMessage());

            return self::FAILURE;
        }

        if ($this->option('fresh')) {
            $this->truncateDestination();
        }

        $this->info('Importando páginas y contenido estructurado…');
        $pages = $this->sourcePosts(['page', 'post']);
        $this->importPages($pages);

        $this->info('Importando documentos enlazados y documentos del tema…');
        $this->importLinkedDocuments($pages);
        $this->importCustomDocuments();

        $this->info('Importando adjuntos…');
        $this->importMedia((string) $this->option('uploads-path'));

        $this->info('Importando navegación…');
        $this->importNavigation();
        $this->seedSettings();

        $this->newLine();
        $this->info('Importación terminada.');
        $this->table(['Entidad', 'Total'], [
            ['Páginas', Page::count()],
            ['Documentos', Document::count()],
            ['Sesiones', CodisecSession::count()],
            ['Actividades', Activity::count()],
            ['Medios', MediaAsset::count()],
            ['Elementos de menú', NavigationItem::count()],
        ]);

        return self::SUCCESS;
    }

    private function sourcePosts(array $types)
    {
        return DB::connection('wordpress')->table($this->prefix.'posts')
            ->select(['ID', 'post_date', 'post_content', 'post_title', 'post_excerpt', 'post_name', 'post_type', 'menu_order'])
            ->where('post_status', 'publish')
            ->whereIn('post_type', $types)
            ->where('ID', '<>', 2)
            ->orderBy('ID')
            ->get();
    }

    private function importPages($posts): void
    {
        foreach ($posts as $post) {
            $slug = $post->post_name ?: Str::slug($post->post_title);
            Page::updateOrCreate(
                ['wordpress_id' => $post->ID],
                [
                    'title' => $post->post_title,
                    'slug' => $slug,
                    'excerpt' => $post->post_excerpt ?: Str::limit(strip_tags($post->post_content), 220),
                    'content' => $this->sanitizeHtml($post->post_content),
                    'status' => 'published',
                    'template' => in_array($slug, ['actividades', 'sesiones'], true) ? $slug : 'default',
                    'menu_order' => $post->menu_order,
                    'is_featured' => $slug === 'codisec',
                    'published_at' => $this->dateOrNull($post->post_date),
                    'metadata' => ['wordpress_type' => $post->post_type],
                ]
            );

            if ($slug === 'actividades') {
                $this->extractActivities($post);
            }
            if ($slug === 'sesiones') {
                $this->extractSessions($post);
            }
        }
    }

    private function importLinkedDocuments($posts): void
    {
        foreach ($posts as $post) {
            if (in_array($post->post_name, ['actividades', 'codisec'], true)) {
                continue;
            }
            $category = DocumentCategory::updateOrCreate(
                ['slug' => $post->post_name ?: Str::slug($post->post_title)],
                ['name' => $post->post_title, 'is_active' => true]
            );
            foreach ($this->anchors($post->post_content) as $index => $anchor) {
                if (! $this->looksLikeDocument($anchor['url'])) {
                    continue;
                }
                $title = $anchor['title'] ?: 'Documento '.($index + 1);
                $slug = Str::slug($title).'-wp-'.$post->ID.'-'.($index + 1);
                Document::updateOrCreate(
                    ['slug' => $slug],
                    [
                        'document_category_id' => $category->id,
                        'title' => $title,
                        'external_url' => $this->normalizeUrl($anchor['url']),
                        'mime_type' => $this->mimeFromUrl($anchor['url']),
                        'status' => 'published',
                        'published_at' => $this->dateOrNull($post->post_date),
                        'metadata' => ['source_page_id' => $post->ID, 'source_page_slug' => $post->post_name],
                    ]
                );
            }
        }
    }

    private function importCustomDocuments(): void
    {
        $posts = $this->sourcePosts(['lsvrdocument']);
        $category = DocumentCategory::updateOrCreate(
            ['slug' => 'planes-locales-historicos'],
            ['name' => 'Planes locales históricos', 'is_active' => true]
        );
        $meta = DB::connection('wordpress')->table($this->prefix.'postmeta')
            ->whereIn('post_id', $posts->pluck('ID'))
            ->whereIn('meta_key', ['meta_document_file', 'meta_document_type'])
            ->get()->groupBy('post_id');
        foreach ($posts as $post) {
            $rows = $meta->get($post->ID, collect())->keyBy('meta_key');
            $url = $this->firstSerializedValue($rows->get('meta_document_file')->meta_value ?? null);
            Document::updateOrCreate(
                ['wordpress_id' => $post->ID],
                [
                    'document_category_id' => $category->id,
                    'title' => $post->post_title,
                    'slug' => $post->post_name ?: Str::slug($post->post_title).'-'.$post->ID,
                    'description' => $post->post_content ?: null,
                    'external_url' => $url ? $this->normalizeUrl($url) : null,
                    'mime_type' => $rows->get('meta_document_type')->meta_value ?? $this->mimeFromUrl((string) $url),
                    'status' => 'published',
                    'published_at' => $this->dateOrNull($post->post_date),
                ]
            );
        }
    }

    private function importMedia(string $uploadsPath): void
    {
        $posts = DB::connection('wordpress')->table($this->prefix.'posts')
            ->select(['ID', 'post_title', 'post_excerpt', 'post_parent', 'guid', 'post_mime_type', 'post_date'])
            ->where('post_type', 'attachment')->orderBy('ID')->get();
        $files = DB::connection('wordpress')->table($this->prefix.'postmeta')->where('meta_key', '_wp_attached_file')->pluck('meta_value', 'post_id');
        $altTexts = DB::connection('wordpress')->table($this->prefix.'postmeta')->where('meta_key', '_wp_attachment_image_alt')->pluck('meta_value', 'post_id');
        $copyFiles = $uploadsPath !== '' && File::isDirectory($uploadsPath);
        $destinationRoot = storage_path('app/public/legacy');
        if ($copyFiles) {
            File::ensureDirectoryExists($destinationRoot);
        }
        $bar = $this->output->createProgressBar($posts->count());
        foreach ($posts as $post) {
            $relative = str_replace('\\', '/', (string) ($files[$post->ID] ?? ''));
            $storedPath = null;
            $fileSize = null;
            if ($copyFiles && $relative !== '' && ! str_contains($relative, '..')) {
                $source = rtrim($uploadsPath, '\\/').DIRECTORY_SEPARATOR.str_replace('/', DIRECTORY_SEPARATOR, $relative);
                $destination = $destinationRoot.DIRECTORY_SEPARATOR.str_replace('/', DIRECTORY_SEPARATOR, $relative);
                if (File::isFile($source)) {
                    File::ensureDirectoryExists(dirname($destination));
                    if (! File::exists($destination) || File::size($source) !== File::size($destination)) {
                        File::copy($source, $destination);
                    }
                    $storedPath = 'legacy/'.$relative;
                    $fileSize = File::size($destination);
                }
            }
            MediaAsset::updateOrCreate(
                ['wordpress_id' => $post->ID],
                [
                    'title' => $post->post_title ?: null,
                    'alt_text' => $altTexts[$post->ID] ?? $post->post_excerpt ?: null,
                    'file_path' => $storedPath,
                    'original_url' => $this->normalizeUrl($post->guid),
                    'mime_type' => $post->post_mime_type ?: $this->mimeFromUrl($relative),
                    'file_size' => $fileSize,
                    'wordpress_parent_id' => $post->post_parent ?: null,
                    'metadata' => ['wordpress_relative_path' => $relative, 'wordpress_date' => $post->post_date],
                ]
            );
            $bar->advance();
        }
        $bar->finish();
        $this->newLine();
        if ($copyFiles) {
            $this->copyRemainingUploads($uploadsPath, $destinationRoot);
        }
        if ($uploadsPath !== '' && ! $copyFiles) {
            $this->warn('La ruta de uploads no existe; se conservaron las URLs originales sin copiar archivos.');
        }
    }

    private function copyRemainingUploads(string $uploadsPath, string $destinationRoot): void
    {
        $this->info('Verificando archivos no registrados como adjuntos…');
        $sourceRoot = rtrim((string) realpath($uploadsPath), '\\/').DIRECTORY_SEPARATOR;
        foreach (File::allFiles($uploadsPath) as $file) {
            $relative = str_replace('\\', '/', substr($file->getPathname(), strlen($sourceRoot)));
            $destination = $destinationRoot.DIRECTORY_SEPARATOR.str_replace('/', DIRECTORY_SEPARATOR, $relative);
            File::ensureDirectoryExists(dirname($destination));
            if (! File::exists($destination) || $file->getSize() !== File::size($destination)) {
                File::copy($file->getPathname(), $destination);
            }
        }
    }

    private function extractSessions(object $post): void
    {
        foreach ($this->anchors($post->post_content) as $index => $anchor) {
            if (! $this->looksLikeDocument($anchor['url'])) {
                continue;
            }
            $title = $anchor['title'] ?: 'Sesión CODISEC '.($index + 1);
            CodisecSession::updateOrCreate(
                ['slug' => Str::slug($title).'-wp-'.$post->ID.'-'.($index + 1)],
                [
                    'wordpress_id' => $post->ID,
                    'title' => $title,
                    'description' => 'Documento migrado desde la sección de sesiones de CODISEC.',
                    'external_url' => $this->normalizeUrl($anchor['url']),
                    'status' => 'published',
                    'metadata' => ['year' => $this->extractYear($anchor['title'].' '.$anchor['url']), 'source_index' => $index],
                ]
            );
        }
    }

    private function extractActivities(object $post): void
    {
        preg_match_all('/<(h1|h2)\b[^>]*>(.*?)<\/\1>/isu', $post->post_content, $headings, PREG_OFFSET_CAPTURE);
        $count = count($headings[0]);
        for ($index = 0; $index < $count; $index++) {
            $heading = $headings[0][$index][0];
            $start = $headings[0][$index][1] + strlen($heading);
            $end = $index + 1 < $count ? $headings[0][$index + 1][1] : strlen($post->post_content);
            $title = trim(html_entity_decode(strip_tags($headings[2][$index][0]), ENT_QUOTES | ENT_HTML5, 'UTF-8'));
            $content = trim(substr($post->post_content, $start, $end - $start));
            if (mb_strlen($title) < 4 || mb_strlen(strip_tags($content)) < 20) {
                continue;
            }
            preg_match('/<img\b[^>]*src=["\']([^"\']+)["\']/iu', $content, $image);
            Activity::updateOrCreate(
                ['slug' => Str::slug($title).'-wp-'.$post->ID.'-'.($index + 1)],
                [
                    'wordpress_id' => $post->ID,
                    'title' => Str::limit($title, 255, ''),
                    'summary' => Str::limit(trim(preg_replace('/\s+/u', ' ', strip_tags($content))), 320),
                    'content' => $this->sanitizeHtml($content),
                    'occurred_at' => $this->extractSpanishDate($content),
                    'cover_image' => isset($image[1]) ? $this->normalizeUrl(html_entity_decode($image[1], ENT_QUOTES | ENT_HTML5, 'UTF-8')) : null,
                    'status' => 'published',
                    'published_at' => $this->dateOrNull($post->post_date),
                    'metadata' => ['source_heading_index' => $index],
                ]
            );
        }
    }

    private function importNavigation(): void
    {
        $items = DB::connection('wordpress')->table($this->prefix.'posts')->where('post_type', 'nav_menu_item')->where('post_status', 'publish')->orderBy('menu_order')->get();
        $metas = DB::connection('wordpress')->table($this->prefix.'postmeta')
            ->whereIn('post_id', $items->pluck('ID'))
            ->whereIn('meta_key', ['_menu_item_object_id', '_menu_item_menu_item_parent', '_menu_item_url', '_menu_item_target'])
            ->get()->groupBy('post_id');
        $pages = Page::query()->whereNotNull('wordpress_id')->get()->keyBy('wordpress_id');
        $created = [];
        foreach ($items as $item) {
            $meta = $metas->get($item->ID, collect())->pluck('meta_value', 'meta_key');
            $page = $pages->get((int) ($meta['_menu_item_object_id'] ?? 0));
            $label = trim($item->post_title) ?: $page?->title ?: 'Enlace';
            $url = $page ? $this->publicPathForPage($page) : ($meta['_menu_item_url'] ?? '#');
            $created[$item->ID] = NavigationItem::updateOrCreate(
                ['wordpress_id' => $item->ID],
                ['label' => $label, 'url' => $this->normalizeUrl((string) $url), 'target' => $meta['_menu_item_target'] ?: '_self', 'sort_order' => $item->menu_order, 'is_active' => true]
            );
        }
        foreach ($items as $item) {
            $meta = $metas->get($item->ID, collect())->pluck('meta_value', 'meta_key');
            $parentWordPressId = (int) ($meta['_menu_item_menu_item_parent'] ?? 0);
            if ($parentWordPressId && isset($created[$parentWordPressId], $created[$item->ID])) {
                $created[$item->ID]->update(['parent_id' => $created[$parentWordPressId]->id]);
            }
        }
    }

    private function seedSettings(): void
    {
        foreach (['site_name' => 'CODISEC Surquillo', 'site_tagline' => 'Comité Distrital de Seguridad Ciudadana', 'municipality' => 'Municipalidad Distrital de Surquillo', 'contact_email' => 'codisec@munisurquillo.gob.pe'] as $key => $value) {
            SiteSetting::updateOrCreate(['key' => $key], ['value' => $value, 'group' => 'general']);
        }
    }

    private function anchors(string $html): array
    {
        preg_match_all('/<a\b[^>]*href=(["\'])(.*?)\1[^>]*>(.*?)<\/a>/isu', $html, $matches, PREG_SET_ORDER);

        return collect($matches)->map(fn (array $match) => [
            'url' => trim(html_entity_decode($match[2], ENT_QUOTES | ENT_HTML5, 'UTF-8')),
            'title' => trim(preg_replace('/\s+/u', ' ', html_entity_decode(strip_tags($match[3]), ENT_QUOTES | ENT_HTML5, 'UTF-8'))),
        ])->filter(fn (array $anchor) => $anchor['url'] !== '' && $anchor['url'] !== '#')->values()->all();
    }

    private function looksLikeDocument(string $url): bool
    {
        return (bool) preg_match('/\.(pdf|docx?|xlsx?|pptx?|mp4)(?:[?#].*)?$/iu', $url)
            || str_contains($url, 'gob.pe/institucion/')
            || str_contains($url, 'cdn.www.gob.pe/uploads/document/');
    }

    private function sanitizeHtml(string $html): string
    {
        $html = preg_replace('/\[\/?vc_[^\]]+\]/iu', '', $html) ?? $html;
        $html = preg_replace('#<(script|object|embed)\b[^>]*>.*?</\1>#isu', '', $html) ?? $html;
        $html = preg_replace('/\s+on[a-z]+\s*=\s*(["\']).*?\1/isu', '', $html) ?? $html;
        $html = preg_replace('/\s+(href|src)\s*=\s*(["\'])\s*javascript:.*?\2/isu', '', $html) ?? $html;

        return preg_replace_callback('/(href|src)=(["\'])(.*?)\2/iu', fn (array $match) => $match[1].'='.$match[2].$this->normalizeUrl(html_entity_decode($match[3], ENT_QUOTES | ENT_HTML5, 'UTF-8')).$match[2], $html) ?? $html;
    }

    private function normalizeUrl(string $url): string
    {
        $url = trim($url);
        $url = preg_replace('#^https?://(www\.)?munisurquillo\.gob\.pe/codisec/wp-content/uploads/#i', '/storage/legacy/', $url) ?? $url;

        return str_replace(' ', '%20', $url);
    }

    private function publicPathForPage(Page $page): string
    {
        return match ($page->slug) {
            'inicio', 'bienvenido-a-codisec-surquillo' => '/',
            'actividades' => '/actividades',
            'sesiones' => '/sesiones',
            default => '/seccion/'.$page->slug,
        };
    }

    private function firstSerializedValue(?string $value): ?string
    {
        if (! $value) {
            return null;
        }
        $data = @unserialize($value, ['allowed_classes' => false]);

        return is_array($data) ? (string) reset($data) : $value;
    }

    private function mimeFromUrl(string $url): ?string
    {
        $extension = strtolower((string) pathinfo(parse_url($url, PHP_URL_PATH) ?: '', PATHINFO_EXTENSION));

        return match ($extension) {
            'pdf' => 'application/pdf', 'doc' => 'application/msword', 'docx' => 'application/vnd.openxmlformats-officedocument.wordprocessingml.document',
            'xls' => 'application/vnd.ms-excel', 'xlsx' => 'application/vnd.openxmlformats-officedocument.spreadsheetml.sheet',
            'jpg', 'jpeg' => 'image/jpeg', 'png' => 'image/png', 'mp4' => 'video/mp4', default => null,
        };
    }

    private function dateOrNull(?string $value): ?Carbon
    {
        return (! $value || str_starts_with($value, '0000-00-00')) ? null : Carbon::parse($value, 'America/Lima');
    }

    private function extractYear(string $value): ?int
    {
        preg_match('/\b(20[0-3][0-9])\b/', $value, $match);

        return isset($match[1]) ? (int) $match[1] : null;
    }

    private function extractSpanishDate(string $value): ?string
    {
        $text = mb_strtolower(strip_tags($value));
        $months = ['enero' => 1, 'febrero' => 2, 'marzo' => 3, 'abril' => 4, 'mayo' => 5, 'junio' => 6, 'julio' => 7, 'agosto' => 8, 'septiembre' => 9, 'setiembre' => 9, 'octubre' => 10, 'noviembre' => 11, 'diciembre' => 12];
        preg_match('/\b([0-3]?\d)\s+de\s+('.implode('|', array_keys($months)).')\s+(?:de(?:l)?\s+)?(20\d{2})\b/u', $text, $match);
        if (! isset($match[3])) {
            return null;
        }
        try {
            return Carbon::create((int) $match[3], $months[$match[2]], (int) $match[1])->toDateString();
        } catch (Throwable) {
            return null;
        }
    }

    private function truncateDestination(): void
    {
        DB::statement('SET FOREIGN_KEY_CHECKS=0');
        foreach ([NavigationItem::class, MediaAsset::class, CodisecSession::class, Activity::class, Document::class, DocumentCategory::class, Page::class, SiteSetting::class] as $model) {
            $model::truncate();
        }
        DB::statement('SET FOREIGN_KEY_CHECKS=1');
    }
}
