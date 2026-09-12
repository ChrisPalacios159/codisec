<?php

use App\Http\Controllers\ActivityController;
use App\Http\Controllers\ContactController;
use App\Http\Controllers\DocumentController;
use App\Http\Controllers\HomeController;
use App\Http\Controllers\PageController;
use App\Http\Controllers\SessionController;
use App\Models\Document;
use App\Models\Page;
use Illuminate\Support\Facades\Route;

Route::get('/', HomeController::class)->name('home');
Route::get('/seccion/{page:slug}', [PageController::class, 'show'])->name('pages.show');
Route::get('/documentos', [DocumentController::class, 'index'])->name('documents.index');
Route::get('/documentos/{document:slug}', [DocumentController::class, 'show'])->name('documents.show');
Route::get('/actividades', [ActivityController::class, 'index'])->name('activities.index');
Route::get('/actividades/{activity:slug}', [ActivityController::class, 'show'])->name('activities.show');
Route::get('/sesiones', [SessionController::class, 'index'])->name('sessions.index');
Route::get('/contacto', [ContactController::class, 'create'])->name('contact.create');
Route::post('/contacto', [ContactController::class, 'store'])->middleware('throttle:5,1')->name('contact.store');

Route::get('/wp-content/uploads/{path}', fn (string $path) => redirect('/storage/legacy/'.$path, 301))->where('path', '.*');
Route::get('/{legacySlug}', function (string $legacySlug) {
    if ($page = Page::published()->where('slug', $legacySlug)->first()) {
        if ($legacySlug === 'actividades') {
            return redirect()->route('activities.index', status: 301);
        }
        if ($legacySlug === 'sesiones') {
            return redirect()->route('sessions.index', status: 301);
        }

        return redirect()->route('pages.show', $page, 301);
    }
    if ($document = Document::published()->where('slug', $legacySlug)->first()) {
        return redirect()->route('documents.show', $document, 301);
    }
    abort(404);
})->where('legacySlug', '[A-Za-z0-9-]+');
