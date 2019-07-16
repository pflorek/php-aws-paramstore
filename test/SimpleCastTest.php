<?php

namespace PFlorek\AwsParameterStore;

use PHPUnit\Framework\TestCase;
use stdClass;
use TypeError;

class SimpleCastTest extends TestCase
{
    /**
     * @test
     * @expectedException TypeError
     */
    public function parseValue_WithObject_ShouldReturn()
    {
        // Given
        $given = new stdClass();

        // When
        SimpleCast::cast($given);
    }

    /**
     * @test
     * @expectedException TypeError
     */
    public function parseValue_WithArray_ShouldReturn()
    {
        // Given
        $given = [];

        // When
        SimpleCast::cast($given);
    }

    /**
     * @test
     */
    public function parseValue_WithNumeric_ShouldFloat()
    {
        // Given
        $given = ' .123';

        // When
        $actual = SimpleCast::cast($given);

        // Then
        $this->assertSame(.123, $actual);
        $this->assertInternalType('float', $actual);
    }

    /**
     * @test
     */
    public function parseValue_WithNumeric_ShouldReturnInt()
    {
        // Given
        $given = ' 123';

        // When
        $actual = SimpleCast::cast($given);

        // Then
        $this->assertSame(123, $actual);
        $this->assertInternalType('int', $actual);
    }

    /**
     * @test
     */
    public function parseValue_WithBoolString_ShouldReturnBool()
    {
        // Given
        $given = 'no';

        // When
        $actual = SimpleCast::cast($given);

        // Then
        $this->assertFalse($actual);
        $this->assertInternalType('bool', $actual);

    }

    /**
     * @test
     */
    public function parseValue_WithAnyString_ShouldReturnString()
    {
        // Given
        $given = 'foo';

        // When
        $actual = SimpleCast::cast($given);

        // Then
        $this->assertSame($given, $actual);
        $this->assertInternalType('string', $actual);
    }
}