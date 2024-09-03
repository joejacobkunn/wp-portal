<?php

namespace App\Rules;

use App\Services\Kenect;
use Closure;
use Illuminate\Contracts\Validation\ValidationRule;

class ValidAssigneeForSMS implements ValidationRule
{
    /**
     * Run the validation rule.
     *
     * @param  \Closure(string): \Illuminate\Translation\PotentiallyTranslatedString  $fail
     */
    public function validate(string $attribute, mixed $value, Closure $fail): void
    {
        $kenet = new Kenect();
        $response = $kenet->teams();
        $teams = array_column(json_decode($response['body']), 'name');

        if (!in_array(trim($value), $teams)) {
            $fail("Teams not found");
        }
    }
}
