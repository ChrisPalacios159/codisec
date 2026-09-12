<?php

namespace App\Filament\Resources\CodisecSessions\Pages;

use App\Filament\Resources\CodisecSessions\CodisecSessionResource;
use Filament\Actions\EditAction;
use Filament\Resources\Pages\ViewRecord;

class ViewCodisecSession extends ViewRecord
{
    protected static string $resource = CodisecSessionResource::class;

    protected function getHeaderActions(): array
    {
        return [
            EditAction::make(),
        ];
    }
}
