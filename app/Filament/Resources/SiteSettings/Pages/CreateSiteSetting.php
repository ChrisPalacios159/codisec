<?php

namespace App\Filament\Resources\SiteSettings\Pages;

use App\Filament\Resources\SiteSettings\SiteSettingResource;
use App\Filament\Support\ConfirmedCreateRecord;

class CreateSiteSetting extends ConfirmedCreateRecord
{
    protected static string $resource = SiteSettingResource::class;
}
