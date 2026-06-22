<?php

namespace Tests\Feature;

use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class ModerationTest extends TestCase
{
    use RefreshDatabase;

    public function test_admin_can_suspend_user_and_suspended_user_cannot_post(): void
    {
        $admin = User::factory()->create(['is_admin' => true]);
        $user = User::factory()->create();

        $this->actingAs($admin)->post(route('admin.users.suspend', $user), [
            'reason' => 'Repeated spam after previous moderation warnings.',
        ])->assertSessionHasNoErrors();

        $this->assertNotNull($user->fresh()->suspended_at);
        $this->actingAs($user)->post(route('questions.store'), [
            'title' => 'This should not be created',
            'body' => str_repeat('Blocked content ', 4),
            'tags' => 'spam',
        ])->assertSessionHas('error');
        $this->assertDatabaseCount('questions', 0);
    }
}
