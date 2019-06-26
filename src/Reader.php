<?php

namespace PFlorek\AwsParamstore;

use Aws\Ssm\SsmClient;
use PFlorek\Elevator\Elevator;

class Reader
{
    /**
     * @var SsmClient
     */
    private $client;

    /**
     * @var Elevator
     */
    private $elevator;

    /**
     * @var Options
     */
    private $options;

    /**
     * @var Parser
     */
    private $parser;

    /**
     * @param SsmClient $client
     * @param Elevator $elevator
     * @param Options $options
     */
    public function __construct(SsmClient $client, Elevator $elevator, Options $options)
    {
        $this->client = $client;
        $this->elevator = $elevator;
        $this->options = $options;
        $this->parser = new Parser();
    }

    /**
     * @see https://docs.aws.amazon.com/aws-sdk-php/v3/api/api-ssm-2014-11-06.html#getparametersbypath
     * @return mixed[]
     */
    public function read()
    {
        if (!$this->options->isEnabled()) {
            return [];
        }

        $context = $this->createContext();
        $result = $this->client->getParametersByPath([
            'Path' => $context,
            'Recursive' => true,
            'WithDecryption' => true,
        ]);
        $parameters = $result['Parameters'];

        $config = [];
        foreach ($parameters as $parameter) {
            $key = $parameter['Name'];
            $key = str_replace(["{$context}/", '/'], ['', '.'], $key);
            $value = $parameter['Value'];
            $value = $this->parser->parseValue($value);
            $config[$key] = $value;
        }

        return $this->elevator->up($config);
    }

    /**
     * @return string
     */
    private function createContext()
    {
        $prefix = trim($this->options->getPrefix(), '/');
        $name = trim($this->options->getName(), '/');

        return "/{$prefix}/{$name}";
    }
}