<?php

namespace PhilKra\Helper;

use PhilKra\Exception\MissingAppNameException;

/**
 *
 * Agent Config Store
 *
 */
class Config
{
    const DEFAULT_APM_VERSION = 'v1';

    /**
     * Config Set
     *
     * @var array
     */
    private $config;

    /**
     * @param array $config
     */
    public function __construct(array $config)
    {
        if (isset($config['appName']) === false) {
            throw new MissingAppNameException();
        }

        // Register Merged Config
        $this->config = array_merge($this->getDefaultConfig(), $config);
    }

    /**
     * Get Config Value
     *
     * @param string $key
     * @param mixed $default
     *
     * @return mixed: value | null
     */
    public function get(string $key, $default = null)
    {
        return ($this->config[$key]) ?? $default;
    }

    /**
     * Get the all Config Set as array
     *
     * @return array
     */
    public function asArray() : array
    {
        return $this->config;
    }

    public function apmVersion(): string
    {
        return $this->config['apmVersion'];
    }

    public function useVersion1(): bool
    {
        return $this->config['apmVersion'] === 'v1';
    }

    public function useVersion2(): bool
    {
        return $this->config['apmVersion'] === 'v2';
    }

    /**
     * Get the Default Config of the Agent
     *
     * @link https://github.com/philkra/elastic-apm-php-agent/issues/55
     *
     * @return array
     */
    private function getDefaultConfig() : array
    {
        return [
            'serverUrl'      => 'http://127.0.0.1:8200',
            'secretToken'    => null,
            'hostname'       => gethostname(),
            'appVersion'     => '',
            'active'         => true,
            'timeout'        => 5,
            'apmVersion'     => 'v1',
            'env'            => [],
            'cookies'        => [],
            'httpClient'     => [],
            'environment'    => 'development',
            'backtraceLimit' => 0,
        ];
    }
}
