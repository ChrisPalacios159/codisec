<?php

namespace App\Filament\Resources\CodisecSessions\Pages;

use App\Filament\Resources\CodisecSessions\CodisecSessionResource;
use App\Filament\Support\ConfirmedEditRecord;
use Filament\Actions\DeleteAction;
use Filament\Actions\ViewAction;

class EditCodisecSession extends ConfirmedEditRecord
{
    protected static string $resource = CodisecSessionResource::class;

    protected function getHeaderActions(): array
    {
        return [
            ViewAction::make(),
            DeleteAction::make(),
        ];
    }
}
