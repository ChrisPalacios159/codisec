<?php

namespace App\Http\Controllers;

use App\Models\Document;
use App\Models\DocumentCategory;
use Illuminate\Http\Request;

class DocumentController extends Controller
{
    public function index(Request $request)
    {
        $documents = Document::published()->with('category')
            ->when($request->filled('q'), fn ($query) => $query->where('title', 'like', '%'.str_replace(['%', '_'], ['\\%', '\\_'], $request->string('q')).'%'))
            ->when($request->filled('categoria'), fn ($query) => $query->whereHas('category', fn ($category) => $category->where('slug', $request->string('categoria'))))
            ->latest('published_at')->paginate(18)->withQueryString();
        $categories = DocumentCategory::query()->where('is_active', true)->orderBy('name')->get();

        return view('documents.index', compact('documents', 'categories'));
    }

    public function show(Document $document)
    {
        abort_unless($document->status === 'published', 404);

        return view('documents.show', compact('document'));
    }
}
