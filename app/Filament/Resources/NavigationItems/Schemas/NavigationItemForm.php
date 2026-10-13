<?php

namespace App\Filament\Resources\NavigationItems\Schemas;

use Filament\Forms\Components\Select;
use Filament\Forms\Components\Textarea;
use Filament\Forms\Components\TextInput;
use Filament\Forms\Components\Toggle;
use Filament\Schemas\Schema;

class NavigationItemForm
{
    public static function configure(Schema $schema): Schema
    {
        return $schema
            ->components([
                TextInput::make('wordpress_id')
                    ->label('Identificador anterior')
                    ->numeric()
                    ->default(null),
                Select::make('parent_id')
                    ->label('Elemento superior')
                    ->relationship('parent', 'id')
                    ->default(null),
                TextInput::make('label')
                    ->label('Texto del menú')
                    ->required(),
                Textarea::make('url')
                    ->label('Dirección web')
                    ->required()
                    ->columnSpanFull(),
                TextInput::make('target')
                    ->label('Forma de apertura')
                    ->required()
                    ->default('_self'),
                TextInput::make('sort_order')
                    ->label('Orden de aparición')
                    ->required()
                    ->numeric()
                    ->minValue(0)
                    ->default(0),
                Toggle::make('is_active')
                    ->label('Enlace activo')
                    ->required(),
            ]);
    }
}
