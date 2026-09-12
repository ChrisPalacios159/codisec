<?php

namespace App\Http\Controllers;

use App\Models\CodisecSession;

class SessionController extends Controller
{
    public function index()
    {
        $sessions = CodisecSession::published()->latest('session_date')->latest('id')->paginate(20);

        return view('sessions.index', compact('sessions'));
    }
}
