<?php

namespace App\Filament\Support;

use App\Filament\Resources\Documents\DocumentResource;
use Filament\Actions\CreateAction;
use Filament\Resources\Pages\ListRecords;

abstract class ListDocumentSection extends ListRecords
{
    public function getTitle(): string
    {
        $resource = static::getResource();

        return $resource::getSectionTitle();
    }

    public function getSubheading(): ?string
    {
        $resource = static::getResource();

        return $resource::getSectionDescription();
    }

    protected function getHeaderActions(): array
    {
        $resource = static::getResource();

        return [
            CreateAction::make()
                ->label('Agregar documento')
                ->url(fn (): string => DocumentResource::getUrl(
                    'create',
                    array_filter(['category' => $resource::getCategoryId()]),
                    panel: 'admin',
                )),
        ];
    }
}
