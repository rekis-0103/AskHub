<?php

namespace Tests\Feature;

use App\Models\Question;
use App\Models\User;
use App\Notifications\ContentActivityNotification;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\Notification;
use Tests\TestCase;

class PersonalizationTest extends TestCase
{
    use RefreshDatabase;

    public function test_user_can_save_and_follow_a_question(): void
    {
        $owner = User::factory()->create();
        $viewer = User::factory()->create();
        $question = Question::create(['user_id' => $owner->id, 'title' => 'Personalization question', 'body' => str_repeat('Details ', 5)]);

        $this->actingAs($viewer)->post(route('bookmarks.store', $question));
        $this->actingAs($viewer)->post(route('questions.follow', $question));

        $this->assertDatabaseHas('bookmarks', ['user_id' => $viewer->id, 'question_id' => $question->id]);
        $this->assertDatabaseHas('follows', ['user_id' => $viewer->id, 'followable_id' => $question->id]);
        $this->get(route('dashboard'))->assertOk()->assertSee($question->title);
    }

    public function test_question_owner_and_followers_are_notified_of_a_new_answer(): void
    {
        Notification::fake();
        $owner = User::factory()->create();
        $follower = User::factory()->create();
        $answerer = User::factory()->create();
        $question = Question::create(['user_id' => $owner->id, 'title' => 'Notification question', 'body' => str_repeat('Details ', 5)]);
        $this->actingAs($follower)->post(route('questions.follow', $question));

        $this->actingAs($answerer)->post(route('answers.store', $question), [
            'body' => 'This complete answer contains enough detail to pass validation.',
        ]);

        Notification::assertSentTo([$owner, $follower], ContentActivityNotification::class);
        Notification::assertNotSentTo($answerer, ContentActivityNotification::class);
    }
}
