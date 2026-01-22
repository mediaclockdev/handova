<?php

namespace App\Http\Controllers;

use App\Http\Controllers\Controller;
use App\Models\PageContent;
use Illuminate\Http\Request;

class PageContentDetailsController extends Controller
{
    public function __construct()
    {
        $this->middleware('auth');
    }

    /**
     * Display list
     */
    public function index()
    {
        $pages = PageContent::orderBy('id', 'desc')->paginate(10);

        return view('admin.page_content.index', compact('pages'));
    }

    /**
     * Show form to create new page
     */
    public function create()
    {
        return view('pagecontents.create');
    }

    /**
     * Store new page
     */
    public function store(Request $request)
    {
        $request->validate([
            'title'       => 'required|string|max:255',
            'type'        => 'required|in:terms_and_conditions,privacy_policy',
            'description' => 'required',
        ]);

        PageContent::create($request->only('title', 'type', 'description'));

        return redirect()
            ->route('pagecontents.index')
            ->with('success', 'Page content created successfully.');
    }

    /**
     * Show edit form
     */
    public function edit($id)
    {
        $pagecontent = PageContent::findOrFail($id);

        return view('pagecontents.edit', compact('pagecontent'));
    }

    /**
     * Update page
     */
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
            ->route('pagecontents.index')
            ->with('success', 'Page content updated successfully.');
    }

    /**
     * Delete page
     */
    public function destroy($id)
    {
        $pagecontent = PageContent::findOrFail($id);
        $pagecontent->delete();

        return redirect()
            ->route('pagecontents.index')
            ->with('success', 'Page content deleted successfully.');
    }
}
