<?php

namespace App\Filament\Resources\Activities\Pages;

use App\Filament\Resources\Activities\ActivityResource;
use App\Filament\Support\ConfirmedCreateRecord;

class CreateActivity extends ConfirmedCreateRecord
{
    protected static string $resource = ActivityResource::class;
}
