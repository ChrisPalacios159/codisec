<?php

namespace App\Filament\Resources\CodisecSessions\Pages;

use App\Filament\Resources\CodisecSessions\CodisecSessionResource;
use App\Filament\Support\ConfirmedCreateRecord;

class CreateCodisecSession extends ConfirmedCreateRecord
{
    protected static string $resource = CodisecSessionResource::class;
}
