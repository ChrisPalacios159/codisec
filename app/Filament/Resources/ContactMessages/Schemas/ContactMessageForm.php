<?php

namespace App\Filament\Resources\ContactMessages\Schemas;

use Filament\Forms\Components\DateTimePicker;
use Filament\Forms\Components\Textarea;
use Filament\Forms\Components\TextInput;
use Filament\Schemas\Schema;

class ContactMessageForm
{
    public static function configure(Schema $schema): Schema
    {
        return $schema
            ->components([
                TextInput::make('name')
                    ->label('Nombre')
                    ->required(),
                TextInput::make('email')
                    ->label('Correo electrónico')
                    ->email()
                    ->required(),
                TextInput::make('phone')
                    ->label('Teléfono')
                    ->tel()
                    ->default(null),
                TextInput::make('subject')
                    ->label('Asunto')
                    ->default(null),
                Textarea::make('message')
                    ->label('Mensaje')
                    ->required()
                    ->columnSpanFull(),
                DateTimePicker::make('read_at')->label('Fecha de lectura'),
                TextInput::make('status')
                    ->label('Estado')
                    ->required()
                    ->default('new'),
            ]);
    }
}
