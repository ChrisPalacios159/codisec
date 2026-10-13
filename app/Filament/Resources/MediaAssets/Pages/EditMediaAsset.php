<?php

namespace App\Filament\Resources\MediaAssets\Pages;

use App\Filament\Resources\MediaAssets\MediaAssetResource;
use App\Filament\Support\ConfirmedEditRecord;
use Filament\Actions\DeleteAction;
use Filament\Actions\ViewAction;

class EditMediaAsset extends ConfirmedEditRecord
{
    protected static string $resource = MediaAssetResource::class;

    protected function getHeaderActions(): array
    {
        return [
            ViewAction::make(),
            DeleteAction::make(),
        ];
    }
}
