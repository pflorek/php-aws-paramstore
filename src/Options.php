<?php

namespace PFlorek\AwsParamstore;

class Options
{
    /**
     * @var string
     */
    private $prefix;

    /**
     * @var string
     */
    private $name;

    /**
     * @var bool
     */
    private $enabled;

    /**
     * @param string $prefix
     * @param string $name
     * @param bool $enabled
     */
    public function __construct($prefix, $name, $enabled)
    {
        $this->prefix = $prefix;
        $this->name = $name;
        $this->enabled = $enabled;
    }

    /**
     * @return string
     */
    public function getPrefix()
    {
        return $this->prefix;
    }

    /**
     * @return string
     */
    public function getName()
    {
        return $this->name;
    }

    /**
     * @return bool
     */
    public function isEnabled()
    {
        return $this->enabled;
    }
}