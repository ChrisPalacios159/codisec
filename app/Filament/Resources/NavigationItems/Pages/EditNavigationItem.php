<?php

namespace App\Filament\Resources\NavigationItems\Pages;

use App\Filament\Resources\NavigationItems\NavigationItemResource;
use App\Filament\Support\ConfirmedEditRecord;
use Filament\Actions\DeleteAction;
use Filament\Actions\ViewAction;

class EditNavigationItem extends ConfirmedEditRecord
{
    protected static string $resource = NavigationItemResource::class;

    protected function getHeaderActions(): array
    {
        return [
            ViewAction::make(),
            DeleteAction::make(),
        ];
    }
}
