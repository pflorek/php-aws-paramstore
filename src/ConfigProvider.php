<?php


namespace PFlorek\AwsParameterStore;


use Aws\Ssm\SsmClient;
use function PFlorek\Elevator\array_elevate;

class ConfigProvider
{
    /**
     * @var Options
     */
    private $options;

    /**
     * @var Reader
     */
    private $reader;

    /**
     * @var string[]
     */
    private $profiles;

    /**
     * @param array|SsmClient|Reader $client
     * @param string[]|Options $options
     * @param null|string|string[] $profiles
     */
    public function __construct($client, $options, $profiles = [])
    {
        if (is_array($options)) {
            $options = Options::create($options);
        }
        $this->options = $options;

        if(!$client instanceof Reader) {
            $client = new Reader($client);
        }
        $this->reader = $client;

        $this->profiles = (array)$profiles;
    }

    /**
     * @return string[]|int[]|float[]|bool[]
     */
    public function __invoke(): array
    {
        $paths = $this->createPaths($this->profiles);
        $paths = array_reverse($paths);

        $config = [];
        foreach ($paths as $context) {
            $config += $this->reader->fromPath($context);
        }

        $config = array_map([SimpleCast::class, 'cast'], $config);

        return array_elevate($config);
    }

    /**
     * @param string[] $profiles
     * @return string[]
     */
    private function createPaths(array $profiles): array
    {
        $prefix = $this->options->getPrefix();
        $prefix = trim($prefix, '/');

        $sharedContext = $this->options->getSharedContext();
        $sharedContext = trim($sharedContext, '/');

        $name = $this->options->getApplicationName();
        $name = trim($name, '/');

        $separator = $this->options->getProfileSeparator();

        $paths = [];
        if ($sharedContext) {
            $paths[] = "/{$prefix}/{$sharedContext}";
        }

        $paths[] = "/{$prefix}/{$name}"; // base context

        foreach ($profiles as $profile) {
            $profile = trim($profile, '/');
            $paths[] = "/{$prefix}/{$name}{$separator}{$profile}";
        }

        return $paths;
    }
}