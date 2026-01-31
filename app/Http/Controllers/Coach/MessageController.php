<?php

namespace App\Http\Controllers\Coach;

use App\Http\Controllers\Controller;
use Illuminate\View\View;

class MessageController extends Controller
{
    public function index(): View
    {
        return view('coach.messages.index');
    }

    public function show(int $userId): View
    {
        return view('coach.messages.show', ['userId' => $userId]);
    }
}
