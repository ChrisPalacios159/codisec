<?php

namespace App\Filament\Resources\InformesCumplimiento;

use App\Filament\Resources\InformesCumplimiento\Pages\ListInformesCumplimiento;
use App\Filament\Support\DocumentSectionResource;

class InformesCumplimientoResource extends DocumentSectionResource
{
    protected static ?string $slug = 'informes-trimestrales-de-cumplimiento';

    protected static string $categorySlug = 'informes-trimestrales-de-cumplimiento';

    protected static string $sectionTitle = 'Informes Trimestrales de Cumplimiento';

    protected static string $sectionDescription = 'Evidencias y resultados trimestrales del cumplimiento de metas y compromisos.';

    protected static ?string $modelLabel = 'informe de cumplimiento';

    protected static ?string $pluralModelLabel = 'informes de cumplimiento';

    public static function getPages(): array
    {
        return ['index' => ListInformesCumplimiento::route('/')];
    }
}
