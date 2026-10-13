<?php

namespace App\Filament\Resources\Documents\Schemas;

use Filament\Forms\Components\DateTimePicker;
use Filament\Forms\Components\FileUpload;
use Filament\Forms\Components\RichEditor;
use Filament\Forms\Components\Select;
use Filament\Forms\Components\TextInput;
use Filament\Forms\Components\Toggle;
use Filament\Schemas\Schema;

use function Filament\Support\original_request;

class DocumentForm
{
    public static function configure(Schema $schema): Schema
    {
        return $schema->columns(2)->components([
            TextInput::make('title')->label('Título')->required()->maxLength(255),
            TextInput::make('slug')->label('URL amigable')->required()->unique(ignoreRecord: true)->maxLength(255),
            Select::make('document_category_id')
                ->label('Sección del sitio')
                ->relationship('category', 'name')
                ->default(fn (): ?int => original_request()->integer('category') ?: null)
                ->disabled(fn (): bool => original_request()->integer('category') > 0)
                ->dehydrated()
                ->helperText(fn (): ?string => original_request()->integer('category') > 0 ? 'Asignada automáticamente desde el módulo seleccionado.' : null)
                ->searchable()
                ->preload()
                ->required(),
            Select::make('status')->label('Estado')->options(['draft' => 'Borrador', 'published' => 'Publicado'])->required(),
            FileUpload::make('file_path')->label('Archivo local')->disk('public')->directory('documents')->visibility('public')->acceptedFileTypes(['application/pdf', 'application/msword', 'application/vnd.openxmlformats-officedocument.wordprocessingml.document'])->columnSpanFull(),
            TextInput::make('external_url')->label('URL externa')->url()->columnSpanFull(),
            RichEditor::make('description')->label('Descripción')->columnSpanFull(),
            DateTimePicker::make('published_at')->label('Fecha de publicación'),
            Toggle::make('is_featured')->label('Destacar en portada'),
        ]);
    }
}
