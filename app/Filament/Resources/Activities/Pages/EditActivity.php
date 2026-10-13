<?php

namespace App\Filament\Resources\Activities\Pages;

use App\Filament\Resources\Activities\ActivityResource;
use App\Filament\Support\ConfirmedEditRecord;
use Filament\Actions\DeleteAction;
use Filament\Actions\ViewAction;

class EditActivity extends ConfirmedEditRecord
{
    protected static string $resource = ActivityResource::class;

    protected function getHeaderActions(): array
    {
        return [
            ViewAction::make(),
            DeleteAction::make(),
        ];
    }
}
