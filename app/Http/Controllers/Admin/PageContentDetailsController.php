<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;  // ← FIXED
use App\Models\PageContent;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class PageContentDetailsController extends Controller
{
    public function __construct()
    {
        $this->middleware('auth');
    }

    public function index()
    {
        $formTitle = 'Page Content';
        $pages = PageContent::orderBy('id', 'desc')->paginate(10);
        return view('admin.page_content.index', compact('formTitle', 'pages'));
    }

    public function create()
    {
        $formTitle = 'Page Content';
        return view('admin.page_content.create', compact('formTitle'));
    }

    public function store(Request $request)
    {
        $request->validate([
            'title'       => 'required|string|max:255',
            'type'        => 'required|in:terms_and_conditions,privacy_policy',
            'description' => 'required',
        ]);

        PageContent::create($request->only('title', 'type', 'description'));

        return redirect()
            ->route('admin.page_content.index')
            ->with('success', 'Page content created successfully.');
    }

    public function edit($id)
    {
        $pagecontent = PageContent::findOrFail($id);
        $formTitle = 'Page Content';
        return view('admin.page_content.edit', compact('pagecontent', 'formTitle'));
    }

    public function update(Request $request, $id)
    {
        $request->validate([
            'title'       => 'required|string|max:255',
            'type'        => 'required|in:terms_and_conditions,privacy_policy',
            'description' => 'required',
        ]);

        $pagecontent = PageContent::findOrFail($id);
        $pagecontent->update($request->only('title', 'type', 'description'));

        return redirect()
            ->route('admin.page_content.index')
            ->with('success', 'Page content updated successfully.');
    }

    public function destroy($id)
    {
        $pagecontent = PageContent::findOrFail($id);
        $pagecontent->delete();

        return redirect()
            ->route('admin.page_content.index')
            ->with('success', 'Page content deleted successfully.');
    }
}
