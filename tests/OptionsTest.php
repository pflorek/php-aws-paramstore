<?php

namespace PFlorek\AwsParamstore;

use PHPUnit\Framework\TestCase;

class OptionsTest extends TestCase
{
    /**
     * @test
     */
    public function getPrefix_WithPrefix_WillReturnPrefix()
    {
        $given = 'foo';
        $options = new Options($given, null, null);

        $actual = $options->getPrefix();

        $this->assertSame($given, $actual);
    }

    /**
     * @test
     */
    public function getName_WithName_WillReturnName()
    {
        $given = 'foo';
        $options = new Options(null, $given, null);

        $actual = $options->getName();

        $this->assertSame($given, $actual);
    }

    /**
     * @test
     */
    public function getEnabled_WithEnabled_WillReturnEnabled()
    {
        $given = true;
        $options = new Options(null, null, $given);

        $actual = $options->isEnabled();

        $this->assertSame($given, $actual);
    }
}