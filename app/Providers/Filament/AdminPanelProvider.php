<?php

namespace App\Providers\Filament;

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
use App\Models\Page;
use Filament\Http\Middleware\Authenticate;
use Filament\Http\Middleware\AuthenticateSession;
use Filament\Http\Middleware\DisableBladeIconComponents;
use Filament\Http\Middleware\DispatchServingFilamentEvent;
use Filament\Navigation\NavigationGroup;
use Filament\Navigation\NavigationItem;
use Filament\Pages\Dashboard;
use Filament\Panel;
use Filament\PanelProvider;
use Filament\Support\Colors\Color;
use Filament\Support\Enums\Width;
use Filament\Support\Icons\Heroicon;
use Filament\View\PanelsRenderHook;
use Filament\Widgets\AccountWidget;
use Illuminate\Cookie\Middleware\AddQueuedCookiesToResponse;
use Illuminate\Cookie\Middleware\EncryptCookies;
use Illuminate\Foundation\Http\Middleware\PreventRequestForgery;
use Illuminate\Routing\Middleware\SubstituteBindings;
use Illuminate\Session\Middleware\StartSession;
use Illuminate\View\Middleware\ShareErrorsFromSession;
use Illuminate\View\View;

class AdminPanelProvider extends PanelProvider
{
    public function panel(Panel $panel): Panel
    {
        return $panel
            ->default()
            ->id('admin')
            ->path('admin')
            ->viteTheme('resources/css/filament/admin/theme.css')
            ->spa(hasPrefetching: true)
            ->renderHook(
                PanelsRenderHook::BODY_START,
                fn (): View => view('filament.components.loading-overlay'),
            )
            ->login()
            ->brandName('CODISEC | Panel de contenidos')
            ->colors([
                'primary' => Color::Teal,
            ])
            ->darkMode(false)
            ->themeSwitcher(false)
            ->maxContentWidth(Width::Full)
            ->sidebarCollapsibleOnDesktop()
            ->sidebarWidth('18rem')
            ->navigationGroups([
                NavigationGroup::make('Secciones del sitio')
                    ->icon(Heroicon::OutlinedSquares2x2)
                    ->collapsible(false),
                NavigationGroup::make('Atención ciudadana')
                    ->icon(Heroicon::OutlinedChatBubbleLeftRight)
                    ->collapsible(false),
                NavigationGroup::make('Herramientas')
                    ->icon(Heroicon::OutlinedWrenchScrewdriver)
                    ->collapsed(),
                NavigationGroup::make('Administración')
                    ->icon(Heroicon::OutlinedCog6Tooth)
                    ->collapsed(),
            ])
            ->navigationItems([
                NavigationItem::make('CODISEC')
                    ->group('Secciones del sitio')
                    ->icon(Heroicon::OutlinedBuildingLibrary)
                    ->sort(1)
                    ->url(fn (): string => $this->pageEditorUrl('codisec')),
                NavigationItem::make('Normas Legales')
                    ->group('Secciones del sitio')
                    ->icon(Heroicon::OutlinedScale)
                    ->sort(2)
                    ->url(fn (): string => $this->documentSectionUrl('normas-legales')),
                NavigationItem::make('Directorio')
                    ->group('Secciones del sitio')
                    ->icon(Heroicon::OutlinedIdentification)
                    ->sort(3)
                    ->url(fn (): string => $this->pageEditorUrl('directorio')),
                NavigationItem::make('Sesiones')
                    ->group('Secciones del sitio')
                    ->icon(Heroicon::OutlinedCalendarDays)
                    ->sort(4)
                    ->url(fn (): string => CodisecSessionResource::getUrl(panel: 'admin')),
                NavigationItem::make('Evaluación de Desempeño')
                    ->group('Secciones del sitio')
                    ->icon(Heroicon::OutlinedChartBarSquare)
                    ->sort(5)
                    ->url(fn (): string => $this->documentSectionUrl('evaluacion-de-desempeno')),
                NavigationItem::make('Actividades y Buenas Prácticas')
                    ->group('Secciones del sitio')
                    ->icon(Heroicon::OutlinedMegaphone)
                    ->sort(6)
                    ->url(fn (): string => ActivityResource::getUrl(panel: 'admin')),
                NavigationItem::make('Plan Local de Seguridad Ciudadana')
                    ->group('Secciones del sitio')
                    ->icon(Heroicon::OutlinedClipboardDocumentCheck)
                    ->sort(7)
                    ->url(fn (): string => $this->documentSectionUrl('plan-local-de-seguridad-ciudadana')),
                NavigationItem::make('Boletines Estadísticos')
                    ->group('Secciones del sitio')
                    ->icon(Heroicon::OutlinedNewspaper)
                    ->sort(8)
                    ->url(fn (): string => $this->documentSectionUrl('boletines-estadisticos')),
                NavigationItem::make('Informes Trimestrales de Seguimiento')
                    ->group('Secciones del sitio')
                    ->icon(Heroicon::OutlinedArrowTrendingUp)
                    ->sort(9)
                    ->url(fn (): string => $this->documentSectionUrl('informes-trimestrales-de-seguimiento')),
                NavigationItem::make('Informes Trimestrales de Cumplimiento')
                    ->group('Secciones del sitio')
                    ->icon(Heroicon::OutlinedCheckBadge)
                    ->sort(10)
                    ->url(fn (): string => $this->documentSectionUrl('informes-trimestrales-de-cumplimiento')),
            ])
            ->discoverResources(in: app_path('Filament/Resources'), for: 'App\Filament\Resources')
            ->discoverPages(in: app_path('Filament/Pages'), for: 'App\Filament\Pages')
            ->pages([
                Dashboard::class,
            ])
            ->discoverWidgets(in: app_path('Filament/Widgets'), for: 'App\Filament\Widgets')
            ->widgets([
                AccountWidget::class,
            ])
            ->middleware([
                EncryptCookies::class,
                AddQueuedCookiesToResponse::class,
                StartSession::class,
                AuthenticateSession::class,
                ShareErrorsFromSession::class,
                PreventRequestForgery::class,
                SubstituteBindings::class,
                DisableBladeIconComponents::class,
                DispatchServingFilamentEvent::class,
            ])
            ->authMiddleware([
                Authenticate::class,
            ]);
    }

    private function pageEditorUrl(string $slug): string
    {
        $page = Page::query()->where('slug', $slug)->first();

        return $page
            ? PageResource::getUrl('edit', ['record' => $page], panel: 'admin')
            : PageResource::getUrl(panel: 'admin');
    }

    private function documentSectionUrl(string $slug): string
    {
        $resource = match ($slug) {
            'normas-legales' => NormasLegalesResource::class,
            'evaluacion-de-desempeno' => EvaluacionDesempenoResource::class,
            'plan-local-de-seguridad-ciudadana' => PlanLocalSeguridadResource::class,
            'boletines-estadisticos' => BoletinesEstadisticosResource::class,
            'informes-trimestrales-de-seguimiento' => InformesSeguimientoResource::class,
            'informes-trimestrales-de-cumplimiento' => InformesCumplimientoResource::class,
            default => DocumentResource::class,
        };

        return $resource::getUrl(panel: 'admin');
    }
}
