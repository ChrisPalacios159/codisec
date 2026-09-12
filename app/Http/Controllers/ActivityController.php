<?php

namespace App\Http\Controllers;

use App\Models\Activity;

class ActivityController extends Controller
{
    public function index()
    {
        $activities = Activity::published()->latest('occurred_at')->latest('id')->paginate(12);

        return view('activities.index', compact('activities'));
    }

    public function show(Activity $activity)
    {
        abort_unless($activity->status === 'published', 404);

        return view('activities.show', compact('activity'));
    }
}
