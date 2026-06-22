<?php

namespace Tests\Feature;

use App\Models\Answer;
use App\Models\Question;
use App\Models\User;
use App\Models\XpTransaction;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class QuestionIntegrityTest extends TestCase
{
    use RefreshDatabase;

    public function test_best_answer_xp_is_awarded_only_once(): void
    {
        $owner = User::factory()->create();
        $author = User::factory()->create();
        $question = Question::create(['user_id' => $owner->id, 'title' => 'A specific test question', 'body' => str_repeat('Details ', 5)]);
        $answer = Answer::create(['question_id' => $question->id, 'user_id' => $author->id, 'body' => str_repeat('Answer ', 5)]);

        $this->actingAs($owner)->post(route('answers.markAsBest', $answer))->assertSessionHasNoErrors();
        $this->actingAs($owner)->post(route('answers.markAsBest', $answer))->assertSessionHasNoErrors();

        $this->assertSame(50, $author->fresh()->xp);
        $this->assertSame(1, XpTransaction::where('reason', 'best_answer')->count());
    }

    public function test_users_cannot_self_vote_and_vote_totals_remain_consistent(): void
    {
        $owner = User::factory()->create();
        $voter = User::factory()->create();
        $question = Question::create(['user_id' => $owner->id, 'title' => 'Voting integrity question', 'body' => str_repeat('Details ', 5)]);

        $this->actingAs($owner)->post(route('questions.vote', $question), ['vote' => 1])->assertStatus(422);
        $this->actingAs($voter)->post(route('questions.vote', $question), ['vote' => 1]);
        $this->assertSame(1, $question->fresh()->votes);
        $this->actingAs($voter)->post(route('questions.vote', $question), ['vote' => -1]);
        $this->assertSame(-1, $question->fresh()->votes);
        $this->actingAs($voter)->post(route('questions.vote', $question), ['vote' => -1]);
        $this->assertSame(0, $question->fresh()->votes);
    }
}
