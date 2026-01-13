<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;

class HelpContentsController extends Controller
{
    public function index()
    {
        return view('admin.help.index');
    }
}

