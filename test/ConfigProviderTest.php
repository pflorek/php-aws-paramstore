<?php

namespace PFlorek\AwsParameterStore;

use Aws\Ssm\SsmClient;
use PHPUnit\Framework\TestCase;
use Prophecy\Prophecy\ObjectProphecy;

class ConfigProviderTest extends TestCase
{

    /**
     * @var Options|ObjectProphecy
     */
    private $options;

    /**
     * @var Reader
     */
    private $reader;

    /**
     * @var ConfigProvider
     */
    private $provider;

    protected function setUp()
    {
        $this->options = $this->prophesize(Options::class);
        $this->reader = $this->prophesize(Reader::class);

        $this->provider = new ConfigProvider($this->reader->reveal(), $this->options->reveal());
    }

    /**
     * @test
     */
    public function create_WithArrayConfig_WillReturnInstance()
    {
        // Given
        $options = [
            Options::KEY_PREFIX => '/path/prefix',
            Options::KEY_APPLICATION_NAME => 'app-name',
        ];
        $client = $this->prophesize(SsmClient::class);

        // When
        $provider = ConfigProvider::create($client->reveal(), $options);

        // Then
        $this->assertInstanceOf(ConfigProvider::class, $provider);
    }

    /**
     * @test
     */
    public function read_WithOptions_WillReturnEmptyArray()
    {
        // Given
        $prefix = 'prefix';
        $name = 'app-name';
        $separator = '+';
        $sharedContext = 'shared';
        $profile = 'test';

        $this->options->getPrefix()
            ->shouldBeCalled()
            ->willReturn($prefix);

        $this->options->getApplicationName()
            ->shouldBeCalled()
            ->willReturn($name);

        $this->options->getProfileSeparator()
            ->shouldBeCalled()
            ->willReturn($separator);

        $this->options->getSharedContext()
            ->shouldBeCalled()
            ->willReturn($sharedContext);

        $parameterFromProfileContext = 'value from profile';
        $this->reader->read("/{$prefix}/{$name}{$separator}{$profile}")
            ->shouldBeCalled()
            ->willReturn([
                'test.profile' => $parameterFromProfileContext,
            ]);

        $parameterFromAppContext = 'value from app context';
        $this->reader->read("/{$prefix}/{$name}")
            ->shouldBeCalled()
            ->willReturn([
                'test.app' => $parameterFromAppContext,
                'test.profile' => $parameterFromAppContext,
            ]);

        $parameterFromSharedContext = 'value from shared context';
        $this->reader->read("/{$prefix}/{$sharedContext}")
            ->shouldBeCalled()
            ->willReturn([
                'test.app' => $parameterFromSharedContext,
                'test.profile' => $parameterFromSharedContext,
                'test.context' => $parameterFromSharedContext,
            ]);

        // When
        $config = $this->provider->provide([$profile]);

        // Then
        $expected = [
            'test' => [
                'profile' => $parameterFromProfileContext,
                'app' => $parameterFromAppContext,
                'context' => $parameterFromSharedContext,
            ],
        ];
        $this->assertSame($expected, $config);
    }
}
