<?php

namespace App\Http\Controllers;

use App\Services\MarkdownRenderer;
use Illuminate\Http\Request;

class MarkdownPreviewController extends Controller
{
    public function __invoke(Request $request, MarkdownRenderer $renderer)
    {
        $validated = $request->validate(['body' => ['nullable', 'string', 'max:50000']]);

        return response()->json([
            'html' => $renderer->render($validated['body'] ?? ''),
        ]);
    }
}
