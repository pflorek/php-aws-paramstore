<?php

namespace PFlorek\AwsParameterStore;


class Options
{
    const DEFAULT_PROFILE_SEPARATOR = '_';
    const KEY_PREFIX = 'prefix';
    const KEY_APPLICATION_NAME = 'name';
    const KEY_DEFAULT_CONTEXT = 'sharedContext';
    const KEY_PROFILE_SEPARATOR = 'profileSeparator';

    /**
     * @var string
     */
    private $prefix;

    /**
     * @var string
     */
    private $sharedContext;

    /**
     * @var string
     */
    private $profileSeparator;

    /**
     * @var string
     */
    private $applicationName;

    /**
     * @param string $prefix
     * @param string $applicationName
     * @param string $profileSeparator
     * @param string $sharedContext
     */
    public function __construct(string $prefix, string $applicationName, string $profileSeparator = self::DEFAULT_PROFILE_SEPARATOR, $sharedContext = '')
    {
        $this->prefix = $prefix;
        $this->applicationName = $applicationName;
        $this->sharedContext = $sharedContext;
        $this->profileSeparator = $profileSeparator;
    }

    /**
     * @param string[] $options
     * @return Options
     */
    public static function create(array $options): Options
    {
        $prefix = $options[self::KEY_PREFIX];
        $name = $options[self::KEY_APPLICATION_NAME];
        $sharedContext = $options['defaultContext'] ?? $options[self::KEY_DEFAULT_CONTEXT] ?? '';
        $profileSeparator = $options[self::KEY_PROFILE_SEPARATOR] ?? self::DEFAULT_PROFILE_SEPARATOR;

        return new Options($prefix, $name, $profileSeparator, $sharedContext);
    }

    /**
     * @return string
     */
    public function getPrefix(): string
    {
        return $this->prefix;
    }

    /**
     * @return string
     */
    public function getSharedContext(): string
    {
        return $this->sharedContext;
    }

    /**
     * @return string
     */
    public function getProfileSeparator(): string
    {
        return $this->profileSeparator;
    }

    /**
     * @return string
     */
    public function getApplicationName(): string
    {
        return $this->applicationName;
    }
}