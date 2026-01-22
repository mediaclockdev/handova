<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;

class HelpContentsController extends Controller
{
    public function __construct()
    {
        $this->middleware('auth');
    }
    
    public function index()
    {
        return view('admin.help.index');
    }
}
