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
                    ->label('Identificador anterior')
                    ->numeric()
                    ->default(null),
                TextInput::make('title')
                    ->label('Título')
                    ->default(null),
                Textarea::make('alt_text')
                    ->label('Texto alternativo')
                    ->default(null)
                    ->columnSpanFull(),
                Textarea::make('file_path')
                    ->label('Ruta del archivo')
                    ->default(null)
                    ->columnSpanFull(),
                Textarea::make('original_url')
                    ->label('Dirección original')
                    ->default(null)
                    ->columnSpanFull(),
                TextInput::make('mime_type')
                    ->label('Tipo de archivo')
                    ->default(null),
                TextInput::make('file_size')
                    ->label('Tamaño en bytes')
                    ->numeric()
                    ->default(null),
                TextInput::make('wordpress_parent_id')
                    ->label('Identificador superior anterior')
                    ->numeric()
                    ->default(null),
                Textarea::make('metadata')
                    ->label('Datos adicionales')
                    ->default(null)
                    ->columnSpanFull(),
            ]);
    }
}
