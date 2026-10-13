<?php

namespace App\Filament\Resources\Pages\Pages;

use App\Filament\Resources\Pages\PageResource;
use App\Filament\Support\ConfirmedEditRecord;
use Filament\Actions\DeleteAction;
use Filament\Actions\ViewAction;

class EditPage extends ConfirmedEditRecord
{
    protected static string $resource = PageResource::class;

    protected function getHeaderActions(): array
    {
        return [
            ViewAction::make(),
            DeleteAction::make(),
        ];
    }
}
