<?php

namespace App\Filament\Resources\CodisecSessions\Pages;

use App\Filament\Resources\CodisecSessions\CodisecSessionResource;
use Filament\Actions\CreateAction;
use Filament\Resources\Pages\ListRecords;

class ListCodisecSessions extends ListRecords
{
    protected static string $resource = CodisecSessionResource::class;

    protected function getHeaderActions(): array
    {
        return [
            CreateAction::make(),
        ];
    }
}
