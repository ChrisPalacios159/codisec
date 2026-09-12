<?php

namespace App\Filament\Resources\CodisecSessions\Pages;

use App\Filament\Resources\CodisecSessions\CodisecSessionResource;
use Filament\Actions\DeleteAction;
use Filament\Actions\ViewAction;
use Filament\Resources\Pages\EditRecord;

class EditCodisecSession extends EditRecord
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
