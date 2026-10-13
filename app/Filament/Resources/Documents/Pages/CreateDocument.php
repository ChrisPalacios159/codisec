<?php

namespace App\Filament\Resources\Documents\Pages;

use App\Filament\Resources\Documents\DocumentResource;
use App\Filament\Support\ConfirmedCreateRecord;

class CreateDocument extends ConfirmedCreateRecord
{
    protected static string $resource = DocumentResource::class;
}
