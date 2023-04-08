<?php

namespace App\Rules;

use App\Entities\Shop\Attribute;
use Illuminate\Contracts\Validation\DataAwareRule;
use Illuminate\Contracts\Validation\InvokableRule;

class AttributeValue implements DataAwareRule, InvokableRule
{
    protected $data;

    /**
     * Determine if the validation rule passes.
     *
     * @param  string  $attribute
     * @param  mixed  $value
     * @param \Closure $fail
     * @return void
     */
    public function __invoke($attribute, $value, $fail):void
    {
        $values = array_map('trim', preg_split('#[\r\n]+#', $value));

        foreach ($values as $val) {
            if ($this->data['type'] === Attribute::TYPE_FLOAT && !filter_var($val, FILTER_VALIDATE_FLOAT)) {
                $fail('Значения должны быть типа float (число с плавающей точкой)');
            }
            if ($this->data['type'] === Attribute::TYPE_INTEGER && !filter_var($val, FILTER_VALIDATE_INT)) {
                $fail('Значения должны быть типа integer (цело число)');
            }
        }
    }

    public function setData($data): AttributeValue|static
    {
        $this->data = $data;
        return $this;
    }
}
