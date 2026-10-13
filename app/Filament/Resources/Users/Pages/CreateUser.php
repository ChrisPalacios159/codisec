<?php

namespace App\Filament\Resources\Users\Pages;

use App\Filament\Resources\Users\UserResource;
use App\Filament\Support\ConfirmedCreateRecord;

class CreateUser extends ConfirmedCreateRecord
{
    protected static string $resource = UserResource::class;
}
