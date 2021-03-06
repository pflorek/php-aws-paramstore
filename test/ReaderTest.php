<?php

namespace PFlorek\AwsParameterStore;

use Aws\Ssm\SsmClient;
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
     * @var Reader
     */
    private $reader;

    protected function setUp()
    {
        $this->client = $this->prophesize(SsmClient::class);

        $this->reader = new Reader($this->client->reveal());
    }

    /**
     * @test
     */
    public function readFromPath_WithAnyContext_WillReturnEmptyArray()
    {
        // Given
        $path = 'any';
        $this->client->getParametersByPath(Argument::withEntry('Path', $path))
            ->shouldBeCalled()
            ->willReturn(['Parameters' => []]);

        // When
        $config = $this->reader->fromPath($path);

        // Then
        $this->assertInternalType('array', $config);
    }

    /**
     * @test
     */
    public function readFromPath_WithExistingParam_WillReturnArray()
    {
        // Given

        $path = '/context/path/with/prefix/and/app_profile';
        $key = 'foo.bar';
        $value = 'baz';

        $this->client->getParametersByPath(Argument::withEntry('Path', $path))
            ->shouldBeCalled()
            ->willReturn(['Parameters' => [
                [
                    'Name' => "$path/{$key}",
                    'Value' => $value,
                ],
            ]]);

        // When
        $config = $this->reader->fromPath($path);

        // Then
        $this->assertSame(['foo.bar' => $value], $config);
    }

    /**
     * @test
     */
    public function readFromPath_WithNextToken_WillReturnArray()
    {
        // Given

        $path = '/context/path/with/prefix/and/app_profile';
        $key = 'foo.bar';
        $value = 'baz';

        $first = [
            'Parameters' => [[
                'Name' => "$path/{$key}",
                'Value' => $value,
            ]],
            'NextToken' => 'some token',
        ];
        $second = [
            'Parameters' => [[
                'Name' => "$path/{$key}",
                'Value' => $value,
            ]],
        ];
        $this->client->getParametersByPath(Argument::withEntry('Path', $path))
            ->willReturn($first, $second);

        // When
        $config = $this->reader->fromPath($path);

        // Then
        $this->assertSame(['foo.bar' => $value], $config);
        $this->client->getParametersByPath(Argument::any())->shouldHaveBeenCalledTimes(2);
    }
}