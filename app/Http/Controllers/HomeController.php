<?php

namespace App\Http\Controllers;

use App\Models\Activity;
use App\Models\Document;
use App\Models\DocumentCategory;
use App\Models\Page;

class HomeController extends Controller
{
    public function __invoke()
    {
        return view('home', [
            'intro' => Page::published()->where('slug', 'codisec')->first(),
            'documents' => Document::published()->with('category')->latest('published_at')->limit(8)->get(),
            'activities' => Activity::published()->latest('occurred_at')->latest('id')->limit(6)->get(),
            'categories' => DocumentCategory::query()->where('is_active', true)->withCount(['documents' => fn ($query) => $query->published()])->orderBy('sort_order')->get(),
        ]);
    }
}
