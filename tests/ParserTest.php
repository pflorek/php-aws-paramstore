<?php

namespace PFlorek\AwsParamstore;

use PHPUnit\Framework\TestCase;
use stdClass;

class ParserTest extends TestCase
{
    /**
     * @var Parser
     */
    private $parser;

    /**
     * @test
     */
    public function parseValue_WithObject_ShouldReturn()
    {
        // Given
        $given = new stdClass();

        // When
        $actual = $this->parser->parseValue($given);

        // Then
        $this->assertSame($given, $actual);
        $this->assertInternalType('object', $actual);
    }

    /**
     * @test
     */
    public function parseValue_WithArray_ShouldReturn()
    {
        // Given
        $given = [];

        // When
        $actual = $this->parser->parseValue($given);

        // Then
        $this->assertSame($given, $actual);
        $this->assertInternalType('array', $actual);
    }

    /**
     * @test
     */
    public function parseValue_WithNumeric_ShouldFloat()
    {
        // Given
        $given = ' .123';

        // When
        $actual = $this->parser->parseValue($given);

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
        $actual = $this->parser->parseValue($given);

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
        $actual = $this->parser->parseValue($given);

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
        $actual = $this->parser->parseValue($given);

        // Then
        $this->assertSame($given, $actual);
        $this->assertInternalType('string', $actual);
    }

    protected function setUp()
    {
        $this->parser = new Parser();
    }
}