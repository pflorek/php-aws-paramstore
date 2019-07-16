<?php

namespace PFlorek\AwsParameterStore;

use PHPUnit\Framework\TestCase;

class OptionsTest extends TestCase
{
    const PREFIX = '/path/prefix/';
    const APP_NAME = 'app';
    const SEPARATOR = '-';
    const CONTEXT = 'shared';

    /**
     * @test
     */
    public function create_WithAllOptions_WillHaveThem()
    {
        $given = [
            Options::KEY_PREFIX => self::PREFIX,
            Options::KEY_APPLICATION_NAME => self::APP_NAME,
            Options::KEY_PROFILE_SEPARATOR => self::SEPARATOR,
            Options::KEY_DEFAULT_CONTEXT => self::CONTEXT,
        ];

        $options = Options::create($given);

        $this->assertSame(self::PREFIX, $options->getPrefix());
        $this->assertSame(self::APP_NAME, $options->getApplicationName());
        $this->assertSame(self::SEPARATOR, $options->getProfileSeparator());
        $this->assertSame(self::CONTEXT, $options->getSharedContext());
    }

    /**
     * @test
     */
    public function create_WithRequiredOptions_WillHaveDefaults()
    {
        $given = [
            Options::KEY_PREFIX => self::PREFIX,
            Options::KEY_APPLICATION_NAME => self::APP_NAME,
        ];

        $options = Options::create($given);

        $this->assertSame(self::PREFIX, $options->getPrefix());
        $this->assertSame(self::APP_NAME, $options->getApplicationName());
        $this->assertSame(Options::DEFAULT_PROFILE_SEPARATOR, $options->getProfileSeparator());
        $this->assertEmpty($options->getSharedContext());
    }
}