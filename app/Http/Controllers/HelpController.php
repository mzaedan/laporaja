<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;

class HelpController extends Controller
{
    /**
     * Display the help and support page.
     */
    public function index()
    {
        return view('pages.help');
    }
}