<?php

namespace App\Filament\Resources\DocumentCategories\Pages;

use App\Filament\Resources\DocumentCategories\DocumentCategoryResource;
use App\Filament\Support\ConfirmedCreateRecord;

class CreateDocumentCategory extends ConfirmedCreateRecord
{
    protected static string $resource = DocumentCategoryResource::class;
}
