<?php

namespace PFlorek\AwsParameterStore;


class SimpleCast
{
    /**
     * @param string $value
     * @return int|float|bool|string
     */
    public static function cast(string $value)
    {
        if (is_numeric($value)) {
            return $value + 0;
        }

        if (filter_var($value, FILTER_VALIDATE_BOOLEAN, FILTER_NULL_ON_FAILURE) !== null) {
            return filter_var($value, FILTER_VALIDATE_BOOLEAN);
        }

        return $value;
    }
}