<?php

namespace App\Http\Controllers\Client;

use App\Http\Controllers\Controller;
use Illuminate\View\View;

class ProgramController extends Controller
{
    public function index(): View
    {
        return view('client.program');
    }
}
