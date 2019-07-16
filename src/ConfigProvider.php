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
     * @param Reader $reader
     * @param Options $options
     */
    public function __construct(Reader $reader, Options $options)
    {
        $this->options = $options;
        $this->reader = $reader;
    }

    /**
     * @param SsmClient $client
     * @param Options|array $options
     * @return ConfigProvider
     */
    public static function create(SsmClient $client, $options): ConfigProvider
    {
        if (!$options instanceof Options) {
            $options = Options::create($options);
        }

        $reader = new Reader($client);

        return new ConfigProvider($reader, $options);
    }

    /**
     * @param string[] $profiles
     * @return string[]|int[]|float[]|bool[]
     */
    public function provide(array $profiles = []): array
    {
        $paths = $this->createPaths($profiles);
        $paths = array_reverse($paths);

        $config = [];
        foreach ($paths as $context) {
            $config += $this->reader->read($context);
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