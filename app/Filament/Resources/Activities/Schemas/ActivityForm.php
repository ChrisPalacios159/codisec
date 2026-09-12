<?php

namespace App\Filament\Resources\Activities\Schemas;

use Filament\Forms\Components\DatePicker;
use Filament\Forms\Components\FileUpload;
use Filament\Forms\Components\RichEditor;
use Filament\Forms\Components\Select;
use Filament\Forms\Components\Textarea;
use Filament\Forms\Components\TextInput;
use Filament\Schemas\Schema;

class ActivityForm
{
    public static function configure(Schema $schema): Schema
    {
        return $schema->columns(2)->components([
            TextInput::make('title')->label('Título')->required()->maxLength(255),
            TextInput::make('slug')->label('URL amigable')->required()->unique(ignoreRecord: true),
            DatePicker::make('occurred_at')->label('Fecha de actividad'),
            Select::make('status')->label('Estado')->options(['draft' => 'Borrador', 'published' => 'Publicado'])->required(),
            Textarea::make('summary')->label('Resumen')->rows(3)->columnSpanFull(),
            FileUpload::make('cover_image')->label('Imagen principal')->disk('public')->directory('activities')->image()->visibility('public')->columnSpanFull(),
            RichEditor::make('content')->label('Contenido')->columnSpanFull(),
        ]);
    }
}
