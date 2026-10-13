<?php

namespace App\Filament\Support;

use App\Filament\Resources\Documents\Tables\DocumentsTable;
use App\Models\Document;
use App\Models\DocumentCategory;
use Filament\Resources\Resource;
use Filament\Tables\Table;
use Illuminate\Database\Eloquent\Builder;

abstract class DocumentSectionResource extends Resource
{
    protected static ?string $model = Document::class;

    protected static bool $shouldRegisterNavigation = false;

    protected static string $categorySlug;

    protected static string $sectionTitle;

    protected static string $sectionDescription;

    public static function table(Table $table): Table
    {
        return DocumentsTable::configure($table, showCategory: false, useCanonicalRoutes: true)
            ->heading(static::getSectionTitle())
            ->description(static::getSectionDescription());
    }

    public static function getEloquentQuery(): Builder
    {
        return parent::getEloquentQuery()
            ->whereHas(
                'category',
                fn (Builder $query): Builder => $query->where('slug', static::$categorySlug),
            );
    }

    public static function getCategoryId(): ?int
    {
        return DocumentCategory::query()
            ->where('slug', static::$categorySlug)
            ->value('id');
    }

    public static function getSectionTitle(): string
    {
        return static::$sectionTitle;
    }

    public static function getSectionDescription(): string
    {
        return static::$sectionDescription;
    }
}
