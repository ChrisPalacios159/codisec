<?php

namespace App\Filament\Resources\Pages\Pages;

use App\Filament\Resources\Pages\PageResource;
use App\Filament\Support\ConfirmedCreateRecord;

class CreatePage extends ConfirmedCreateRecord
{
    protected static string $resource = PageResource::class;
}
