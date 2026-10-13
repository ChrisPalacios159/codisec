<?php

namespace App\Filament\Resources\EvaluacionDesempeno;

use App\Filament\Resources\EvaluacionDesempeno\Pages\ListEvaluacionDesempeno;
use App\Filament\Support\DocumentSectionResource;

class EvaluacionDesempenoResource extends DocumentSectionResource
{
    protected static ?string $slug = 'evaluacion-de-desempeno';

    protected static string $categorySlug = 'evaluacion-de-desempeno';

    protected static string $sectionTitle = 'Evaluación de Desempeño';

    protected static string $sectionDescription = 'Resultados y documentos de evaluación del Comité Distrital de Seguridad Ciudadana.';

    protected static ?string $modelLabel = 'evaluación';

    protected static ?string $pluralModelLabel = 'evaluaciones';

    public static function getPages(): array
    {
        return ['index' => ListEvaluacionDesempeno::route('/')];
    }
}
