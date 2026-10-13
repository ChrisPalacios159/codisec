<?php

namespace App\Filament\Resources\Documents\Pages;

use App\Filament\Resources\Documents\DocumentResource;
use Filament\Actions\CreateAction;
use Filament\Resources\Pages\ListRecords;

class ListDocuments extends ListRecords
{
    protected static string $resource = DocumentResource::class;

    protected function getHeaderActions(): array
    {
        return [
            CreateAction::make()
                ->url(function (): string {
                    $categoryId = data_get($this->tableFilters, 'document_category_id.value');

                    return static::getResource()::getUrl(
                        'create',
                        array_filter(['category' => $categoryId]),
                    );
                }),
        ];
    }
}
