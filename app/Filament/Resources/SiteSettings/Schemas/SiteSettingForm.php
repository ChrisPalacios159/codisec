<?php

namespace App\Filament\Resources\SiteSettings\Schemas;

use Filament\Forms\Components\Textarea;
use Filament\Forms\Components\TextInput;
use Filament\Schemas\Schema;

class SiteSettingForm
{
    public static function configure(Schema $schema): Schema
    {
        return $schema
            ->components([
                TextInput::make('key')
                    ->label('Dato del sitio')
                    ->required(),
                Textarea::make('value')
                    ->label('Contenido')
                    ->default(null)
                    ->columnSpanFull(),
                TextInput::make('type')
                    ->label('Tipo de dato')
                    ->required()
                    ->default('text'),
                TextInput::make('group')
                    ->label('Grupo')
                    ->required()
                    ->default('general'),
            ]);
    }
}
