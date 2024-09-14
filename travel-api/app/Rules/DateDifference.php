<?php

namespace App\Rules;

use App\Models\Travel;
use Carbon\Carbon;
use Closure;
use Illuminate\Contracts\Validation\ValidationRule;

class DateDifference implements ValidationRule
{
    private $startingDate;
    private $endingDate;
    private $expectedDifference;

        public function __construct($startingDate, $endingDate, $expectedDifference) {
        $this->startingDate = $startingDate;
        $this->endingDate = $endingDate;
        $this->expectedDifference = $expectedDifference;
    }

    /**
     * Run the validation rule.
     *
     * @param  \Closure(string): \Illuminate\Translation\PotentiallyTranslatedString  $fail
     */
    public function validate(string $attribute, mixed $value, Closure $fail): void
    {
        if (Carbon::parse($this->startingDate)->diffInDays(Carbon::parse($this->endingDate)) != $this->expectedDifference) {
            $fail('The date difference is invalid.');
        }
    }
}
