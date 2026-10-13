<?php

namespace App\Filament\Resources\MediaAssets\Pages;

use App\Filament\Resources\MediaAssets\MediaAssetResource;
use App\Filament\Support\ConfirmedCreateRecord;

class CreateMediaAsset extends ConfirmedCreateRecord
{
    protected static string $resource = MediaAssetResource::class;
}
