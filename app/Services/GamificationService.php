<?php

namespace App\Services;

use App\Models\Badge;
use App\Models\User;
use App\Models\XpTransaction;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\DB;

class GamificationService
{
    public function award(User $user, string $reason, Model $source): bool
    {
        $amount = (int) config("gamification.awards.$reason", 0);

        if ($amount <= 0) {
            return false;
        }

        $awarded = DB::transaction(function () use ($user, $reason, $source, $amount) {
            $lockedUser = User::query()->lockForUpdate()->findOrFail($user->id);

            $alreadyAwarded = XpTransaction::query()
                ->where('user_id', $lockedUser->id)
                ->where('reason', $reason)
                ->where('source_type', $source::class)
                ->where('source_id', $source->getKey())
                ->exists();

            if ($alreadyAwarded) {
                return false;
            }

            if ($reason !== 'best_answer') {
                $earnedToday = (int) XpTransaction::query()
                    ->where('user_id', $lockedUser->id)
                    ->whereDate('created_at', today())
                    ->where('reason', '!=', 'best_answer')
                    ->sum('amount');

                if ($earnedToday + $amount > config('gamification.daily_participation_cap')) {
                    return false;
                }
            }

            XpTransaction::create([
                'user_id' => $lockedUser->id,
                'amount' => $amount,
                'reason' => $reason,
                'source_type' => $source::class,
                'source_id' => $source->getKey(),
            ]);

            $lockedUser->addXp($amount);

            return true;
        });

        if ($awarded) {
            $this->awardEligibleBadges($user->fresh());
        }

        return $awarded;
    }

    public function awardEligibleBadges(User $user): void
    {
        Badge::query()->get()->each(function (Badge $badge) use ($user) {
            $value = match ($badge->criteria_type) {
                'xp' => $user->xp,
                'answers' => $user->answers()->count(),
                'best_answers' => $user->answers()->where('is_best', true)->count(),
                default => 0,
            };

            if ($value >= $badge->criteria_value) {
                $user->badges()->syncWithoutDetaching([
                    $badge->id => ['awarded_at' => now()],
                ]);
            }
        });
    }
}
