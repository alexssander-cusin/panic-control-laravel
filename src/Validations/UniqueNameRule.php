<?php

namespace PanicControl\Validations;

use Closure;
use Illuminate\Contracts\Validation\ValidationRule;
use PanicControl\Exceptions\PanicControlDoesNotExist;
use PanicControl\Facades\PanicControl;
use PanicControl\Models\PanicControl as PanicControlModel;

class UniqueNameRule implements ValidationRule
{
    public function __construct(
        private string|bool $ignore = false,
    ) {
        //
    }

    /**
     * Run the validation rule.
     */
    public function validate(string $attribute, mixed $value, Closure $fail): void
    {
        if (! in_array(config('panic-control.default'), ['database', 'file'])) {
            $fail('The store configurada não é válida.');
        }

        if ($this->ignore == $value) {
            return;
        }

        if (config('panic-control.default') == 'database' && PanicControlModel::where($attribute, $value)->exists()) {
            $fail('The panic control name exists.');
        }

    }
}
