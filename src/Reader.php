<?php

namespace PFlorek\AwsParameterStore;

use Aws\Ssm\SsmClient;

class Reader
{
    /**
     * @var SsmClient
     */
    private $client;

    /**
     * @param SsmClient $client
     */
    public function __construct(SsmClient $client)
    {
        $this->client = $client;
    }

    /**
     * @see https://docs.aws.amazon.com/aws-sdk-php/v3/api/api-ssm-2014-11-06.html#getparametersbypath
     * @param string $path
     * @return mixed[]
     */
    public function read(string $path): array
    {
        $result = $this->client->getParametersByPath([
            'Path' => $path,
            'Recursive' => true,
            'WithDecryption' => true,
        ]);
        $parameters = $result['Parameters'];

        $config = [];
        foreach ($parameters as $parameter) {
            $name = $parameter['Name'];
            $name = str_replace(["{$path}/", '/'], ['', '.'], $name);
            $value = $parameter['Value'];
            $config[$name] = $value;
        }

        return $config;
    }
}