<?php

namespace Tests\Feature;

use App\Models\Answer;
use App\Models\Question;
use App\Models\Tag;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class QuestionDiscoveryTest extends TestCase
{
    use RefreshDatabase;

    public function test_questions_can_be_searched_filtered_and_filtered_by_tag(): void
    {
        $user = User::factory()->create();
        $laravel = Tag::create(['name' => 'laravel', 'slug' => 'laravel']);
        $matching = Question::create([
            'user_id' => $user->id,
            'title' => 'Laravel queue is not processing jobs',
            'body' => 'I have configured the database queue but the worker remains idle.',
        ]);
        $matching->tags()->attach($laravel);
        Question::create([
            'user_id' => $user->id,
            'title' => 'Centering an element with CSS grid',
            'body' => 'What is the most reliable approach for centering this element?',
        ]);

        $this->get('/questions?q=queue&tag=laravel&status=unanswered')
            ->assertOk()
            ->assertSee($matching->title)
            ->assertDontSee('Centering an element');
    }

    public function test_question_creation_requires_valid_tags_and_persists_them(): void
    {
        $user = User::factory()->create();

        $response = $this->actingAs($user)->post('/questions', [
            'title' => 'How should database transactions wrap related writes?',
            'body' => 'I need both records to succeed or fail together and want a safe pattern.',
            'tags' => 'laravel, mysql',
        ]);

        $question = Question::first();
        $response->assertRedirect($question->public_url);
        $this->assertEqualsCanonicalizing(['laravel', 'mysql'], $question->tags->pluck('name')->all());
    }

    public function test_question_detail_renders_markdown_answers_and_related_content(): void
    {
        $owner = User::factory()->create();
        $author = User::factory()->create();
        $tag = Tag::create(['name' => 'php', 'slug' => 'php']);
        $question = Question::create([
            'user_id' => $owner->id,
            'title' => 'Rendering a complete question page',
            'body' => 'Use **Markdown** and `inline code` in this question body.',
        ]);
        $question->tags()->attach($tag);
        Answer::create([
            'question_id' => $question->id,
            'user_id' => $author->id,
            'body' => 'This answer has sufficient detail to render correctly.',
        ]);

        $this->actingAs($author)->get($question->public_url)
            ->assertOk()
            ->assertSee('<strong>Markdown</strong>', false)
            ->assertSee('This answer has sufficient detail');
    }
}
