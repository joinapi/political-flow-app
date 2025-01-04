<?php

namespace Joinapi\PoliticalFlow\Rules;

use Illuminate\Contracts\Validation\Rule;
use Joinapi\PoliticalFlow\PoliticalFlow;

class Role implements Rule
{
    /**
     * Determine if the validation rule passes.
     *
     * @param  string  $attribute
     * @param  mixed  $value
     */
    public function passes($attribute, $value): bool
    {
        return array_key_exists($value, PoliticalFlow::$roles);
    }

    /**
     * Get the validation error message.
     */
    public function message(): string
    {
        return __('political-flow::default.errors.valid_role');
    }
}
