<?php

namespace App\Filament\Resources\PlanLocalSeguridad;

use App\Filament\Resources\PlanLocalSeguridad\Pages\ListPlanLocalSeguridad;
use App\Filament\Support\DocumentSectionResource;

class PlanLocalSeguridadResource extends DocumentSectionResource
{
    protected static ?string $slug = 'plan-local-de-seguridad-ciudadana';

    protected static string $categorySlug = 'plan-local-de-seguridad-ciudadana';

    protected static string $sectionTitle = 'Plan Local de Seguridad Ciudadana';

    protected static string $sectionDescription = 'Planes locales vigentes e históricos de seguridad ciudadana.';

    protected static ?string $modelLabel = 'plan local';

    protected static ?string $pluralModelLabel = 'planes locales';

    public static function getPages(): array
    {
        return ['index' => ListPlanLocalSeguridad::route('/')];
    }
}
