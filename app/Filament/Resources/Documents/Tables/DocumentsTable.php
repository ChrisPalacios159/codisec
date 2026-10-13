<?php

namespace App\Filament\Resources\Documents\Tables;

use App\Filament\Resources\Documents\DocumentResource;
use App\Models\Document;
use Filament\Actions\BulkActionGroup;
use Filament\Actions\DeleteBulkAction;
use Filament\Actions\EditAction;
use Filament\Actions\ViewAction;
use Filament\Tables\Columns\IconColumn;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Filters\SelectFilter;
use Filament\Tables\Table;

class DocumentsTable
{
    public static function configure(
        Table $table,
        bool $showCategory = true,
        bool $useCanonicalRoutes = false,
    ): Table {
        $columns = [
            TextColumn::make('title')
                ->label('Título')
                ->searchable(),
        ];

        if ($showCategory) {
            $columns[] = TextColumn::make('category.name')
                ->label('Sección')
                ->searchable()
                ->sortable();
        }

        array_push(
            $columns,
            TextColumn::make('slug')
                ->label('Dirección web')
                ->searchable()
                ->toggleable(isToggledHiddenByDefault: true),
            TextColumn::make('mime_type')
                ->label('Tipo de archivo')
                ->toggleable(isToggledHiddenByDefault: true),
            TextColumn::make('file_size')
                ->label('Tamaño')
                ->numeric()
                ->sortable()
                ->toggleable(isToggledHiddenByDefault: true),
            TextColumn::make('status')
                ->label('Estado')
                ->badge()
                ->formatStateUsing(fn (string $state): string => match ($state) {
                    'published' => 'Publicado',
                    'draft' => 'Borrador',
                    default => $state,
                })
                ->color(fn (string $state): string => $state === 'published' ? 'success' : 'warning'),
            IconColumn::make('is_featured')
                ->label('En portada')
                ->boolean(),
            TextColumn::make('published_at')
                ->label('Fecha de publicación')
                ->dateTime('d/m/Y H:i')
                ->sortable()
                ->toggleable(isToggledHiddenByDefault: true),
            TextColumn::make('created_at')
                ->label('Creado')
                ->dateTime('d/m/Y H:i')
                ->sortable()
                ->toggleable(isToggledHiddenByDefault: true),
            TextColumn::make('updated_at')
                ->label('Última modificación')
                ->dateTime('d/m/Y H:i')
                ->sortable()
                ->toggleable(isToggledHiddenByDefault: true),
        );

        return $table
            ->columns($columns)
            ->filters($showCategory ? [
                SelectFilter::make('document_category_id')
                    ->label('Sección del sitio')
                    ->relationship('category', 'name')
                    ->searchable()
                    ->preload(),
            ] : [])
            ->recordActions([
                ViewAction::make()
                    ->url($useCanonicalRoutes
                        ? fn (Document $record): string => DocumentResource::getUrl('view', ['record' => $record], panel: 'admin')
                        : null),
                EditAction::make()
                    ->url($useCanonicalRoutes
                        ? fn (Document $record): string => DocumentResource::getUrl('edit', ['record' => $record], panel: 'admin')
                        : null),
            ])
            ->toolbarActions([
                BulkActionGroup::make([
                    DeleteBulkAction::make(),
                ]),
            ]);
    }
}
