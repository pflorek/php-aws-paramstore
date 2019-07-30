<?php

namespace PFlorek\AwsParameterStore;

use Aws\Result;
use Aws\Ssm\SsmClient;
use Generator;

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
        $config = [];
        foreach ($this->getParameters($path) as $parameter) {
            $name = $parameter['Name'];
            $name = str_replace(["{$path}/", '/'], ['', '.'], $name);
            $value = $parameter['Value'];
            $config[$name] = $value;
        }

        return $config;
    }

    /**
     * @param string $path
     * @return Generator|string[][]
     */
    private function getParameters(string $path)
    {
        $nextToken = null;
        do {
            $result = $this->getParametersByPath($path, $nextToken);
            $parameters = $result['Parameters'];

            foreach ($parameters as $parameter) {
                yield $parameter;
            }

            $nextToken = $result['NextToken'] ?? null;
        } while ($nextToken);
    }

    /**
     * @see https://docs.aws.amazon.com/de_de/systems-manager/latest/APIReference/API_GetParametersByPath.html#API_GetParametersByPath_RequestParameters
     *
     * @param string $path
     * @param string|null $nextToken
     * @return Result|mixed
     */
    private function getParametersByPath(string $path, string $nextToken = null)
    {
        $args = [
            'Path' => $path,
            'Recursive' => true,
            'WithDecryption' => true,
        ];

        if ($nextToken) {
            $args['NextToken'] = $nextToken;
        }

        return $this->client->getParametersByPath($args);
    }
}