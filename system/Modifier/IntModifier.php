<?php

namespace CodeHuiter\Modifier;

class IntModifier
{
    public static function normalizeBetween(int $value, ?int $min = null, ?int $max = null): int
    {
        if ($min !== null && $value < $min) {
            /** @noinspection CallableParameterUseCaseInTypeContextInspection */
            $value = $min;
        }
        if ($max !== null && $value > $max) {
            /** @noinspection CallableParameterUseCaseInTypeContextInspection */
            $value = $max;
        }
        return $value;
    }
}
