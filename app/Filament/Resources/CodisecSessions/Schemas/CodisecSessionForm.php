<?php

namespace App\Filament\Resources\CodisecSessions\Schemas;

use Filament\Forms\Components\DatePicker;
use Filament\Forms\Components\FileUpload;
use Filament\Forms\Components\Select;
use Filament\Forms\Components\Textarea;
use Filament\Forms\Components\TextInput;
use Filament\Schemas\Schema;

class CodisecSessionForm
{
    public static function configure(Schema $schema): Schema
    {
        return $schema->columns(2)->components([
            TextInput::make('title')->label('Título')->required(),
            TextInput::make('slug')->label('URL amigable')->required()->unique(ignoreRecord: true),
            DatePicker::make('session_date')->label('Fecha de sesión'),
            Select::make('status')->label('Estado')->options(['draft' => 'Borrador', 'published' => 'Publicado'])->required(),
            Textarea::make('description')->label('Descripción')->columnSpanFull(),
            FileUpload::make('document_path')->label('Acta o documento')->disk('public')->directory('sessions')->acceptedFileTypes(['application/pdf'])->columnSpanFull(),
            TextInput::make('external_url')->label('URL externa')->url()->columnSpanFull(),
        ]);
    }
}
