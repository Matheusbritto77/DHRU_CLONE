<?php

namespace App\Http\Controllers;

use App\Models\Page;

class AddCreditsController extends Controller
{
    public function __construct()
    {
        $this->middleware(['auth', 'verified']);
    }

    public function index()
    {
        $page = Page::where('slug', 'add-credits')->where('is_active', true)->firstOrFail();

        $blocks = $page->blocks()
            ->where('is_visible', true)
            ->with('plugin')
            ->orderBy('sort_order')
            ->get();

        return view('page-builder', compact('page', 'blocks'));
    }
}
