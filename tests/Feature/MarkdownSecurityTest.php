<?php

namespace Tests\Feature;

use App\Services\MarkdownRenderer;
use Tests\TestCase;

class MarkdownSecurityTest extends TestCase
{
    public function test_markdown_renders_code_but_strips_unsafe_html_and_links(): void
    {
        $html = app(MarkdownRenderer::class)->render(
            "**safe** <script>alert('x')</script> [bad](javascript:alert('x')) `code`"
        );

        $this->assertStringContainsString('<strong>safe</strong>', $html);
        $this->assertStringContainsString('<code>code</code>', $html);
        $this->assertStringNotContainsString('<script', $html);
        $this->assertStringNotContainsString('href="javascript:', $html);
    }
}
