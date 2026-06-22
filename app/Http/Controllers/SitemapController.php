<?php

namespace App\Http\Controllers;

use App\Models\Question;

class SitemapController extends Controller
{
    public function __invoke()
    {
        $questions = Question::query()->select(['id', 'slug', 'updated_at'])
            ->latest('updated_at')->limit(50000)->get();

        return response()->view('sitemap', compact('questions'))
            ->header('Content-Type', 'application/xml');
    }
}
