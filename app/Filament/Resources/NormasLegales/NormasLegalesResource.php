<?php

namespace App\Filament\Resources\NormasLegales;

use App\Filament\Resources\NormasLegales\Pages\ListNormasLegales;
use App\Filament\Support\DocumentSectionResource;

class NormasLegalesResource extends DocumentSectionResource
{
    protected static ?string $slug = 'normas-legales';

    protected static string $categorySlug = 'normas-legales';

    protected static string $sectionTitle = 'Normas Legales';

    protected static string $sectionDescription = 'Ordenanzas, decretos, resoluciones y marco normativo publicados en esta sección.';

    protected static ?string $modelLabel = 'norma legal';

    protected static ?string $pluralModelLabel = 'normas legales';

    public static function getPages(): array
    {
        return ['index' => ListNormasLegales::route('/')];
    }
}
