<?php

namespace PFlorek\AwsParamstore;

use Aws\Ssm\SsmClient;
use PFlorek\Elevator\Elevator;
use PHPUnit\Framework\TestCase;
use Prophecy\Argument;
use Prophecy\Prophecy\ObjectProphecy;

class ReaderTest extends TestCase
{
    /**
     * @var SsmClient|ObjectProphecy
     */
    private $client;

    /**
     * @var Elevator|ObjectProphecy
     */
    private $elevator;

    /**
     * @var Options|ObjectProphecy
     */
    private $options;

    /**
     * @var Reader
     */
    private $reader;

    /**
     * @test
     */
    public function read_WithOptionDisabled_WillReturnConfig()
    {
        $this->options->isEnabled()
            ->shouldBeCalled()
            ->willReturn(false);

        $config = $this->reader->read();

        $this->assertSame([], $config);
    }

    /**
     * @test
     */
    public function read_WithOptions_WillReturnEmptyArray()
    {
        $this->options->isEnabled()
            ->shouldBeCalled()
            ->willReturn(true);

        $this->options->getPrefix()
            ->shouldBeCalled()
            ->willReturn('prefix');

        $this->options->getName()
            ->shouldBeCalled()
            ->willReturn('infix');

        $this->client->getParametersByPath(Argument::any())
            ->shouldBeCalled()
            ->willReturn(['Parameters' => []]);

        $this->elevator->up(Argument::is([]))
            ->shouldBeCalled()
            ->willReturn([]);

        $config = $this->reader->read();

        $this->assertInternalType('array', $config);
    }

    /**
     * @test
     */
    public function read_WithExistingParam_WillReturnArray()
    {
        $prefix = 'prefix';
        $name = 'infix';
        $key = 'foo.bar';
        $value = 'baz';

        $this->options->isEnabled()
            ->shouldBeCalled()
            ->willReturn(true);

        $this->options->getPrefix()
            ->shouldBeCalled()
            ->willReturn($prefix);

        $this->options->getName()
            ->shouldBeCalled()
            ->willReturn($name);

        $this->client->getParametersByPath(Argument::any())
            ->shouldBeCalled()
            ->willReturn(['Parameters' => [
                [
                    'Name' => "/{$prefix}/{$name}/{$key}",
                    'Value' => $value,
                ],
            ]]);

        $this->elevator->up(Argument::is([
            $key => $value,
        ]))
            ->shouldBeCalled()
            ->willReturn([]);

        $config = $this->reader->read();

        $this->assertInternalType('array', $config);
    }

    protected function setUp()
    {
        $this->client = $this->prophesize('\Aws\Ssm\SsmClient');
        $this->elevator = $this->prophesize('PFlorek\Elevator\Elevator');
        $this->options = $this->prophesize('\Tui\Cmc\AwsParamstoreConfig\Options');

        $this->reader = new Reader($this->client->reveal(), $this->elevator->reveal(), $this->options->reveal());
    }
}