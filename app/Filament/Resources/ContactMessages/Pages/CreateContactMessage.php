<?php

namespace App\Filament\Resources\ContactMessages\Pages;

use App\Filament\Resources\ContactMessages\ContactMessageResource;
use App\Filament\Support\ConfirmedCreateRecord;

class CreateContactMessage extends ConfirmedCreateRecord
{
    protected static string $resource = ContactMessageResource::class;
}
