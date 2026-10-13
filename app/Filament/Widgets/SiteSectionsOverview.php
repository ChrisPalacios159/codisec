<?php

namespace App\Filament\Widgets;

use App\Filament\Resources\Activities\ActivityResource;
use App\Filament\Resources\BoletinesEstadisticos\BoletinesEstadisticosResource;
use App\Filament\Resources\CodisecSessions\CodisecSessionResource;
use App\Filament\Resources\Documents\DocumentResource;
use App\Filament\Resources\EvaluacionDesempeno\EvaluacionDesempenoResource;
use App\Filament\Resources\InformesCumplimiento\InformesCumplimientoResource;
use App\Filament\Resources\InformesSeguimiento\InformesSeguimientoResource;
use App\Filament\Resources\NormasLegales\NormasLegalesResource;
use App\Filament\Resources\Pages\PageResource;
use App\Filament\Resources\PlanLocalSeguridad\PlanLocalSeguridadResource;
use App\Models\Activity;
use App\Models\CodisecSession;
use App\Models\DocumentCategory;
use App\Models\Page;
use Filament\Widgets\Widget;

class SiteSectionsOverview extends Widget
{
    protected static bool $isLazy = false;

    protected static ?int $sort = -10;

    protected string $view = 'filament.widgets.site-sections-overview';

    protected int|string|array $columnSpan = 'full';

    /**
     * @return array<string, mixed>
     */
    protected function getViewData(): array
    {
        $definitions = collect([
            ['title' => 'CODISEC', 'slug' => 'codisec', 'type' => 'page', 'icon' => 'heroicon-o-building-library', 'accent' => 'teal', 'description' => 'Presentación, funciones e información institucional del comité.'],
            ['title' => 'Normas Legales', 'slug' => 'normas-legales', 'type' => 'documents', 'icon' => 'heroicon-o-scale', 'accent' => 'sky', 'description' => 'Ordenanzas, decretos, resoluciones y marco normativo.'],
            ['title' => 'Directorio', 'slug' => 'directorio', 'type' => 'page', 'icon' => 'heroicon-o-identification', 'accent' => 'amber', 'description' => 'Integrantes, cargos y entidades que conforman el CODISEC.'],
            ['title' => 'Sesiones', 'slug' => 'sesiones', 'type' => 'sessions', 'icon' => 'heroicon-o-calendar-days', 'accent' => 'violet', 'description' => 'Sesiones ordinarias y extraordinarias con sus actas.'],
            ['title' => 'Evaluación de Desempeño', 'slug' => 'evaluacion-de-desempeno', 'type' => 'documents', 'icon' => 'heroicon-o-chart-bar-square', 'accent' => 'rose', 'description' => 'Resultados y documentos de evaluación del comité.'],
            ['title' => 'Actividades y Buenas Prácticas', 'slug' => 'actividades', 'type' => 'activities', 'icon' => 'heroicon-o-megaphone', 'accent' => 'cyan', 'description' => 'Acciones, noticias, fotografías y buenas prácticas.'],
            ['title' => 'Plan Local de Seguridad Ciudadana', 'slug' => 'plan-local-de-seguridad-ciudadana', 'type' => 'documents', 'icon' => 'heroicon-o-clipboard-document-check', 'accent' => 'emerald', 'description' => 'Planes locales vigentes e históricos de seguridad ciudadana.'],
            ['title' => 'Boletines Estadísticos', 'slug' => 'boletines-estadisticos', 'type' => 'documents', 'icon' => 'heroicon-o-newspaper', 'accent' => 'blue', 'description' => 'Boletines, cifras e información estadística publicada.'],
            ['title' => 'Informes Trimestrales de Seguimiento', 'slug' => 'informes-trimestrales-de-seguimiento', 'type' => 'documents', 'icon' => 'heroicon-o-arrow-trending-up', 'accent' => 'orange', 'description' => 'Avances trimestrales y seguimiento de actividades.'],
            ['title' => 'Informes Trimestrales de Cumplimiento', 'slug' => 'informes-trimestrales-de-cumplimiento', 'type' => 'documents', 'icon' => 'heroicon-o-check-badge', 'accent' => 'lime', 'description' => 'Evidencias y resultados trimestrales de cumplimiento.'],
        ]);

        $pages = Page::query()
            ->whereIn('slug', $definitions->pluck('slug'))
            ->get(['id', 'title', 'slug', 'status'])
            ->keyBy('slug');

        $categories = DocumentCategory::query()
            ->whereIn('slug', $definitions->where('type', 'documents')->pluck('slug'))
            ->withCount('documents')
            ->get(['id', 'name', 'slug'])
            ->keyBy('slug');

        $sections = $definitions->map(function (array $definition) use ($pages, $categories): array {
            $page = $pages->get($definition['slug']);

            return match ($definition['type']) {
                'page' => [
                    ...$definition,
                    'count' => $page ? 1 : 0,
                    'countLabel' => 'página',
                    'manageUrl' => $page
                        ? PageResource::getUrl('edit', ['record' => $page], panel: 'admin')
                        : PageResource::getUrl(panel: 'admin'),
                    'createUrl' => null,
                    'publicUrl' => $page?->status === 'published' ? route('pages.show', $page) : null,
                ],
                'documents' => $this->documentSection($definition, $categories->get($definition['slug']), $page),
                'activities' => [
                    ...$definition,
                    'count' => Activity::query()->count(),
                    'countLabel' => 'actividades',
                    'manageUrl' => ActivityResource::getUrl(panel: 'admin'),
                    'createUrl' => ActivityResource::getUrl('create', panel: 'admin'),
                    'publicUrl' => route('activities.index'),
                ],
                'sessions' => [
                    ...$definition,
                    'count' => CodisecSession::query()->count(),
                    'countLabel' => 'sesiones',
                    'manageUrl' => CodisecSessionResource::getUrl(panel: 'admin'),
                    'createUrl' => CodisecSessionResource::getUrl('create', panel: 'admin'),
                    'publicUrl' => route('sessions.index'),
                ],
            };
        });

        return [
            'sections' => $sections,
        ];
    }

    /**
     * @param  array<string, string>  $definition
     * @return array<string, mixed>
     */
    private function documentSection(array $definition, ?DocumentCategory $category, ?Page $page): array
    {
        $categoryParameters = $category ? ['category' => $category->id] : [];
        $resource = $this->documentResourceForSlug($definition['slug']);

        return [
            ...$definition,
            'count' => $category?->documents_count ?? 0,
            'countLabel' => 'documentos',
            'manageUrl' => $resource::getUrl(panel: 'admin'),
            'createUrl' => DocumentResource::getUrl('create', $categoryParameters, panel: 'admin'),
            'publicUrl' => $page?->status === 'published' ? route('pages.show', $page) : null,
        ];
    }

    /**
     * @return class-string<DocumentResource>
     */
    private function documentResourceForSlug(string $slug): string
    {
        return match ($slug) {
            'normas-legales' => NormasLegalesResource::class,
            'evaluacion-de-desempeno' => EvaluacionDesempenoResource::class,
            'plan-local-de-seguridad-ciudadana' => PlanLocalSeguridadResource::class,
            'boletines-estadisticos' => BoletinesEstadisticosResource::class,
            'informes-trimestrales-de-seguimiento' => InformesSeguimientoResource::class,
            'informes-trimestrales-de-cumplimiento' => InformesCumplimientoResource::class,
            default => DocumentResource::class,
        };
    }
}
