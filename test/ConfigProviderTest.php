<?php

namespace PFlorek\AwsParameterStore;

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

    protected function setUp()
    {
        $this->options = $this->prophesize(Options::class);
        $this->reader = $this->prophesize(Reader::class);
    }

    /**
     * @param null|string|array $profiles
     * @return ConfigProvider
     */
    private function getSut($profiles): ConfigProvider
    {
        return new ConfigProvider($this->reader->reveal(), $this->options->reveal(), $profiles);
    }

    /**
     * @test
     */
    public function create_WithArrayConfig_WillReturnInstance()
    {
        // Given
        $client = [
            'version' => 'latest',
            'region' => 'eu-central-1',
        ];
        $options = [
            Options::KEY_PREFIX => '/path/prefix',
            Options::KEY_APPLICATION_NAME => 'app-name',
        ];

        // When
        $provider = new ConfigProvider($client, $options);

        // Then
        $this->assertInstanceOf(ConfigProvider::class, $provider);
    }

    /**
     * @test
     */
    public function read_WithOptions_WillReturnMergedConfig()
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
        $this->reader->fromPath("/{$prefix}/{$name}{$separator}{$profile}")
            ->shouldBeCalled()
            ->willReturn([
                'test.profile' => $parameterFromProfileContext,
            ]);

        $parameterFromAppContext = 'value from app context';
        $this->reader->fromPath("/{$prefix}/{$name}")
            ->shouldBeCalled()
            ->willReturn([
                'test.app' => $parameterFromAppContext,
                'test.profile' => $parameterFromAppContext,
            ]);

        $parameterFromSharedContext = 'value from shared context';
        $this->reader->fromPath("/{$prefix}/{$sharedContext}")
            ->shouldBeCalled()
            ->willReturn([
                'test.app' => $parameterFromSharedContext,
                'test.profile' => $parameterFromSharedContext,
                'test.context' => $parameterFromSharedContext,
            ]);

        // When
        $provider = $this->getSut($profile);
        $config = $provider();

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

    /**
     * @test
     */
    public function read_WithProfiles_WillMergeThemInOrder()
    {
        // Given
        $prefix = 'prefix';
        $name = 'app-name';
        $separator = '+';
        $firstProfile = 'shared';
        $lastProfile = 'test';

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
            ->willReturn('');

        $parameterFromProfile1Context = 'value from first profile should be overridden';
        $this->reader->fromPath("/{$prefix}/{$name}{$separator}{$firstProfile}")
            ->shouldBeCalled()
            ->willReturn([
                'test.profile' => $parameterFromProfile1Context,
            ]);

        $parameterFromProfile2Context = 'value from last profile should win';
        $this->reader->fromPath("/{$prefix}/{$name}{$separator}{$lastProfile}")
            ->shouldBeCalled()
            ->willReturn([
                'test.profile' => $parameterFromProfile2Context,
            ]);

        $parameterFromAppContext = 'value from app context';
        $this->reader->fromPath("/{$prefix}/{$name}")
            ->shouldBeCalled()
            ->willReturn([
                'test.app' => $parameterFromAppContext,
                'test.profile' => $parameterFromAppContext,
            ]);

        // When
        $provider = $this->getSut([$firstProfile, $lastProfile]);
        $config = $provider();

        // Then
        $expected = [
            'test' => [
                'profile' => $parameterFromProfile2Context,
                'app' => $parameterFromAppContext,
            ],
        ];
        $this->assertSame($expected, $config);
    }
}
