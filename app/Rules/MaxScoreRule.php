<?php

namespace App\Rules;

use App\Models\ScoringCriterion;
use Closure;
use Illuminate\Contracts\Validation\ValidationRule;

class MaxScoreRule implements ValidationRule
{
    public function __construct(
        protected int $criterionId,
    ) {}

    /**
     * Run the validation rule.
     */
    public function validate(string $attribute, mixed $value, Closure $fail): void
    {
        $criterion = ScoringCriterion::find($this->criterionId);

        if (! $criterion) {
            $fail('The scoring criterion does not exist.');
            return;
        }

        if ($value < 0) {
            $fail("Score must be at least 0.");
            return;
        }

        if ($value > $criterion->max_score) {
            $fail("Score for {$criterion->name} cannot exceed {$criterion->max_score}.");
        }
    }
}
