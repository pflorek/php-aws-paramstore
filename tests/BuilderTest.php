<?php

namespace PFlorek\AwsParamstore;

use Aws\Ssm\SsmClient;
use PHPUnit\Framework\TestCase;
use Prophecy\Prophecy\ObjectProphecy;

class BuilderTest extends TestCase
{
    /**
     * @var SsmClient|ObjectProphecy
     */
    private $client;

    /**
     * @var Builder
     */
    private $builder;

    /**
     * @test
     * @expectedException \RuntimeException
     */
    public function build_WithoutPrefix_ShouldThrowException()
    {
        $this->builder->withName('infix');
        $this->builder->withEnabled(false);

        $this->builder->build();
    }

    /**
     * @test
     * @expectedException \RuntimeException
     */
    public function build_WithoutName_ShouldThrowException()
    {
        $this->builder->withPrefix('prefix');
        $this->builder->withEnabled(false);

        $this->builder->build();
    }

    /**
     * @test
     * @expectedException \RuntimeException
     */
    public function build_WithoutEnabled_ShouldThrowException()
    {
        $this->builder->withPrefix('prefix');
        $this->builder->withName('infix');

        $this->builder->build();
    }

    /**
     * @test
     */
    public function build_WithRequiredOptions_ShouldReturnReader()
    {
        $this->builder->withPrefix('prefix');
        $this->builder->withName('infix');
        $this->builder->withEnabled(true);

        $reader = $this->builder->build();

        $this->assertInstanceOf('\Tui\Cmc\AwsParamstoreConfig\Reader', $reader);
    }

    protected function setUp()
    {
        $this->client = $this->prophesize('\Aws\Ssm\SsmClient');

        $this->builder = new Builder($this->client->reveal());
    }
}