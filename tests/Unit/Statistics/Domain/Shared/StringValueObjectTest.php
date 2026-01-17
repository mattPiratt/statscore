<?php

namespace Tests\Unit\Statistics\Domain\Shared;

use App\Statistics\Domain\Shared\StringValueObject;
use InvalidArgumentException;
use PHPUnit\Framework\TestCase;

class StringValueObjectTest extends TestCase
{
    public function test_it_can_be_created_with_valid_string(): void
    {
        $value = 'some-value';
        $stub = new class($value) extends StringValueObject {
        };

        $this->assertEquals($value, $stub->value());
        $this->assertEquals($value, (string)$stub);
    }

    public function test_it_throws_exception_when_value_is_empty(): void
    {
        $this->expectException(InvalidArgumentException::class);
        $this->expectExceptionMessage('Value cannot be empty');

        new class('') extends StringValueObject {
        };
    }

    public function test_it_throws_exception_when_value_is_only_whitespace(): void
    {
        $this->expectException(InvalidArgumentException::class);
        $this->expectExceptionMessage('Value cannot be empty');

        new class('   ') extends StringValueObject {
        };
    }

    public function test_it_can_be_compared_to_another_string_value_object(): void
    {
        $vo1 = new class('value') extends StringValueObject {
        };
        $vo2 = new class('value') extends StringValueObject {
        };
        $vo3 = new class('different') extends StringValueObject {
        };

        $this->assertTrue($vo1->equals($vo2));
        $this->assertFalse($vo1->equals($vo3));
    }
}
