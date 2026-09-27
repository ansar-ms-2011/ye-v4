<?php

namespace App\Casts;

use Illuminate\Contracts\Database\Eloquent\CastsAttributes;
use Illuminate\Database\Eloquent\Model;

class BooleanToYesNo implements CastsAttributes
{
    /**
     * Cast the given value.
     *
     * @param  Model  $model
     * @param  string  $key
     * @param  mixed  $value
     * @param  array  $attributes
     * @return string
     */
    public function get($model, $key, $value, $attributes)
    {
        if ($value == 0) {
            return 'No';
        } elseif ($value == 1) {
            return 'Yes';
        } else {
            return null;
        }
    }

    /**
     * Prepare the given value for storage.
     *
     * @param  Model  $model
     * @param  string  $key
     * @param  array  $value
     * @param  array  $attributes
     * @return int
     */
    public function set($model, $key, $value, $attributes)
    {
        if ($value == 'No' || $value == false) {
            return 0;
        } elseif ($value == 'Yes') {
            return 1;
        } else {
            return null;
        }
    }
}
