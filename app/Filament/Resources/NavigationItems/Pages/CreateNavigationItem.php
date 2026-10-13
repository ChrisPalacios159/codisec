<?php

namespace App\Filament\Resources\NavigationItems\Pages;

use App\Filament\Resources\NavigationItems\NavigationItemResource;
use App\Filament\Support\ConfirmedCreateRecord;

class CreateNavigationItem extends ConfirmedCreateRecord
{
    protected static string $resource = NavigationItemResource::class;
}
