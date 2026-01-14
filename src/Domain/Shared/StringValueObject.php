<?php

namespace App\Domain\Shared;

use InvalidArgumentException;

abstract class StringValueObject
{
    protected string $value;

    public function __construct(string $value)
    {
        if (empty(trim($value))) {
            throw new InvalidArgumentException('Value cannot be empty');
        }
        $this->value = $value;
    }

    public function value(): string
    {
        return $this->value;
    }

    public function __toString(): string
    {
        return $this->value;
    }

    public function equals(self $other): bool
    {
        return $this->value === $other->value;
    }
}
