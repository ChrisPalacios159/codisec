<?php

namespace App\Filament\Resources\MediaAssets\Schemas;

use Filament\Forms\Components\Textarea;
use Filament\Forms\Components\TextInput;
use Filament\Schemas\Schema;

class MediaAssetForm
{
    public static function configure(Schema $schema): Schema
    {
        return $schema
            ->components([
                TextInput::make('wordpress_id')
                    ->numeric()
                    ->default(null),
                TextInput::make('title')
                    ->default(null),
                Textarea::make('alt_text')
                    ->default(null)
                    ->columnSpanFull(),
                Textarea::make('file_path')
                    ->default(null)
                    ->columnSpanFull(),
                Textarea::make('original_url')
                    ->default(null)
                    ->columnSpanFull(),
                TextInput::make('mime_type')
                    ->default(null),
                TextInput::make('file_size')
                    ->numeric()
                    ->default(null),
                TextInput::make('wordpress_parent_id')
                    ->numeric()
                    ->default(null),
                Textarea::make('metadata')
                    ->default(null)
                    ->columnSpanFull(),
            ]);
    }
}
