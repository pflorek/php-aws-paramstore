<?php

namespace PFlorek\AwsParamstore;

class Parser
{
    /**
     * @param mixed|string $value
     * @return mixed|int|float|bool|string
     */
    public function parseValue($value)
    {
        if (!is_string($value)) {
            return $value;
        }

        if (is_numeric($value)) {
            return $value + 0;
        }

        if (filter_var($value, FILTER_VALIDATE_BOOLEAN, FILTER_NULL_ON_FAILURE) !== null) {
            return filter_var($value, FILTER_VALIDATE_BOOLEAN);
        }

        return $value;
    }
}