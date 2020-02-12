<?php

namespace CodeHuiter\Modifier;

use CodeHuiter\Exception\CodeHuiterRuntimeException;

class IntModifier
{
    public static function normalizeBetween(int $value, int $min = null, int $max = null): int
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

    public static function random(int $min, int $max): int
    {
        try {
            return random_int($min, $max);
        } catch (\Exception $exception) {
            throw new CodeHuiterRuntimeException('Cant run random_int command');
        }
    }
}
