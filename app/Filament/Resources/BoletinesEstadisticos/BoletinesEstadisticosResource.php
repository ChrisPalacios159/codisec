<?php

namespace App\Filament\Resources\BoletinesEstadisticos;

use App\Filament\Resources\BoletinesEstadisticos\Pages\ListBoletinesEstadisticos;
use App\Filament\Support\DocumentSectionResource;

class BoletinesEstadisticosResource extends DocumentSectionResource
{
    protected static ?string $slug = 'boletines-estadisticos';

    protected static string $categorySlug = 'boletines-estadisticos';

    protected static string $sectionTitle = 'Boletines Estadísticos';

    protected static string $sectionDescription = 'Boletines, cifras e información estadística publicada para la ciudadanía.';

    protected static ?string $modelLabel = 'boletín estadístico';

    protected static ?string $pluralModelLabel = 'boletines estadísticos';

    public static function getPages(): array
    {
        return ['index' => ListBoletinesEstadisticos::route('/')];
    }
}
