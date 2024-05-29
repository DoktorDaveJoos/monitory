<?php

namespace App\Rules;

use App\Enums\Interval;
use Closure;
use Illuminate\Contracts\Validation\ValidationRule;
use Illuminate\Translation\PotentiallyTranslatedString;

class IntervalLimit implements ValidationRule
{
    /**
     * Run the validation rule.
     *
     * @param  Closure(string): PotentiallyTranslatedString  $fail
     */
    public function validate(string $attribute, mixed $value, Closure $fail): void
    {
        $user = request()->user();

        // Check if the user has a subscription
        if ($user->subscribed()) {
            return;
        }

        // If the user has no subscription, only 5 minutes intervals are allowed
        if ($value == Interval::MINUTES_1->value) {
            $fail("The $attribute must be 5. You can only set 1 minute intervals with a subscription.");
        }
    }
}
