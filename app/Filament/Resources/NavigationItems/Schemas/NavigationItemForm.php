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
                    ->numeric()
                    ->default(null),
                Select::make('parent_id')
                    ->relationship('parent', 'id')
                    ->default(null),
                TextInput::make('label')
                    ->required(),
                Textarea::make('url')
                    ->required()
                    ->columnSpanFull(),
                TextInput::make('target')
                    ->required()
                    ->default('_self'),
                TextInput::make('sort_order')
                    ->required()
                    ->numeric()
                    ->default(0),
                Toggle::make('is_active')
                    ->required(),
            ]);
    }
}
