<?php

namespace PFlorek\AwsParamstore;

use Aws\Ssm\SsmClient;
use PFlorek\Elevator\ElevatorFactory;

class Builder
{
    /**
     * @var SsmClient
     */
    private $client;

    /**
     * @var string
     */
    private $prefix;

    /**
     * @var string
     */
    private $name;

    /**
     * @var string
     */
    private $enabled;

    /**
     * @param SsmClient $client
     */
    public function __construct(SsmClient $client)
    {
        $this->client = $client;
    }

    /**
     * @param string $prefix
     * @return Builder
     */
    public function withPrefix($prefix)
    {
        $this->prefix = $prefix;

        return $this;
    }

    /**
     * @param string $name
     * @return Builder
     */
    public function withName($name)
    {
        $this->name = $name;

        return $this;
    }

    /**
     * @param string $enabled
     * @return Builder
     */
    public function withEnabled($enabled)
    {
        $this->enabled = $enabled;

        return $this;
    }

    /**
     * @return Reader
     */
    public function build()
    {
        if (!$this->prefix) {
            throw new \RuntimeException('Prefix must be not empty');
        }

        if (!$this->name) {
            throw new \RuntimeException('Name must be not empty');
        }

        if ($this->enabled === null) {
            throw new \RuntimeException('Enabled must be set');
        }

        $elevator = ElevatorFactory::getInstance()->create();
        $options = new Options($this->prefix, $this->name, $this->enabled);

        return new Reader($this->client, $elevator, $options);
    }
}