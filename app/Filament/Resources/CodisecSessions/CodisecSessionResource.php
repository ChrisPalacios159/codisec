<?php

namespace App\Filament\Resources\CodisecSessions;

use App\Filament\Resources\CodisecSessions\Pages\CreateCodisecSession;
use App\Filament\Resources\CodisecSessions\Pages\EditCodisecSession;
use App\Filament\Resources\CodisecSessions\Pages\ListCodisecSessions;
use App\Filament\Resources\CodisecSessions\Pages\ViewCodisecSession;
use App\Filament\Resources\CodisecSessions\Schemas\CodisecSessionForm;
use App\Filament\Resources\CodisecSessions\Schemas\CodisecSessionInfolist;
use App\Filament\Resources\CodisecSessions\Tables\CodisecSessionsTable;
use App\Models\CodisecSession;
use BackedEnum;
use Filament\Resources\Resource;
use Filament\Schemas\Schema;
use Filament\Support\Icons\Heroicon;
use Filament\Tables\Table;

class CodisecSessionResource extends Resource
{
    protected static ?string $model = CodisecSession::class;

    protected static string|BackedEnum|null $navigationIcon = Heroicon::OutlinedRectangleStack;

    public static function form(Schema $schema): Schema
    {
        return CodisecSessionForm::configure($schema);
    }

    public static function infolist(Schema $schema): Schema
    {
        return CodisecSessionInfolist::configure($schema);
    }

    public static function table(Table $table): Table
    {
        return CodisecSessionsTable::configure($table);
    }

    public static function getRelations(): array
    {
        return [
            //
        ];
    }

    public static function getPages(): array
    {
        return [
            'index' => ListCodisecSessions::route('/'),
            'create' => CreateCodisecSession::route('/create'),
            'view' => ViewCodisecSession::route('/{record}'),
            'edit' => EditCodisecSession::route('/{record}/edit'),
        ];
    }
}
