<?php

namespace App\Http\Controllers;

use App\Models\Document;
use App\Models\Page;

class PageController extends Controller
{
    public function show(Page $page)
    {
        abort_unless($page->status === 'published', 404);
        $documents = Document::published()->with('category')->whereHas('category', fn ($query) => $query->where('slug', $page->slug))->latest('published_at')->paginate(20);

        return view('pages.show', compact('page', 'documents'));
    }
}
