<?php

namespace App\Rules;

use Closure;
use Illuminate\Contracts\Validation\ValidationRule;
use Illuminate\Support\Carbon;
use Illuminate\Translation\PotentiallyTranslatedString;

class CheckDateRange implements ValidationRule
{
    /**
     * Run the validation rule.
     *
     * @param Closure(string): PotentiallyTranslatedString $fail
     */
    public function validate(string $attribute, mixed $value, Closure $fail): void
    {
        $from = request('from');
        $to = request('to');

        if (!$from && !$to) {
            return;
        }

        if (!$from || !$to) {
            $fail('Both "from" and "to" dates must be provided.');
            return;
        }

        try {
            $fromDate = Carbon::parse($from);
            $toDate = Carbon::parse($to);
        } catch (\Exception $e) {
            $fail('Both "from" and "to" must be valid dates.');
            return;
        }

        $now = Carbon::now();

        if ($fromDate->isFuture() || $toDate->isFuture()) {
            $fail('The "from" and "to" dates cannot be in the future.');
            return;
        }

        if ($fromDate->diffInDays($now) > 30) {
            $fail('The "from" date cannot be more than 30 days before now.');
            return;
        }

        if ($fromDate->diffInHours($toDate) > 4) {
            $fail('The "from" and "to" dates must be at most 4 hours apart.');
            return;
        }
    }
}
