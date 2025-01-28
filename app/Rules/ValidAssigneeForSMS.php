<?php

namespace App\Rules;

use App\Services\Kenect;
use Closure;
use Illuminate\Contracts\Validation\ValidationRule;

class ValidAssigneeForSMS implements ValidationRule
{
    public $teams;

    public function __construct($teams = [])
    {
        $this->teams = $teams;
    }
    /**
     * Run the validation rule.
     *
     * @param  \Closure(string): \Illuminate\Translation\PotentiallyTranslatedString  $fail
     */
    public function validate(string $attribute, mixed $value, Closure $fail): void
    {
        $is_valid = false;

        foreach($this->teams as $team)
        {
            if (str_contains(strtolower(trim($team)),strtolower(trim($value)))) {
                $is_valid = true;
            }
        }

        if (!$is_valid) {
            $fail("Team not found");
        }

    }
}
