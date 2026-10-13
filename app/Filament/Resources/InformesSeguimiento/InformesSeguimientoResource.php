<?php

namespace App\Filament\Resources\InformesSeguimiento;

use App\Filament\Resources\InformesSeguimiento\Pages\ListInformesSeguimiento;
use App\Filament\Support\DocumentSectionResource;

class InformesSeguimientoResource extends DocumentSectionResource
{
    protected static ?string $slug = 'informes-trimestrales-de-seguimiento';

    protected static string $categorySlug = 'informes-trimestrales-de-seguimiento';

    protected static string $sectionTitle = 'Informes Trimestrales de Seguimiento';

    protected static string $sectionDescription = 'Avances trimestrales y seguimiento de las actividades de seguridad ciudadana.';

    protected static ?string $modelLabel = 'informe de seguimiento';

    protected static ?string $pluralModelLabel = 'informes de seguimiento';

    public static function getPages(): array
    {
        return ['index' => ListInformesSeguimiento::route('/')];
    }
}
