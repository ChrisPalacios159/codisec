<?php

namespace App\Filament\Resources\Pages\Schemas;

use Filament\Forms\Components\DateTimePicker;
use Filament\Forms\Components\RichEditor;
use Filament\Forms\Components\Select;
use Filament\Forms\Components\Textarea;
use Filament\Forms\Components\TextInput;
use Filament\Forms\Components\Toggle;
use Filament\Schemas\Schema;

class PageForm
{
    public static function configure(Schema $schema): Schema
    {
        return $schema->columns(2)->components([
            TextInput::make('title')->label('Título')->required()->maxLength(255),
            TextInput::make('slug')->label('URL amigable')->required()->unique(ignoreRecord: true)->maxLength(255),
            Textarea::make('excerpt')->label('Resumen')->rows(3)->columnSpanFull(),
            RichEditor::make('content')->label('Contenido')->columnSpanFull(),
            Select::make('status')->label('Estado')->options(['draft' => 'Borrador', 'published' => 'Publicado'])->required(),
            Select::make('template')->label('Plantilla')->options(['default' => 'Página estándar', 'actividades' => 'Actividades', 'sesiones' => 'Sesiones'])->required(),
            DateTimePicker::make('published_at')->label('Fecha de publicación'),
            TextInput::make('menu_order')->label('Orden')->numeric()->default(0),
            Toggle::make('is_featured')->label('Destacar en portada'),
        ]);
    }
}
