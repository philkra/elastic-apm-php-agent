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

    /**
     * Get the Default Config of the Agent
     *
     * @return array
     */
    private function getDefaultConfig() : array
    {
        return [
            'serverUrl'   => 'http://127.0.0.1:8200',
            'secretToken' => null,
            'hostname'    => gethostname(),
            'appVersion'  => '',
            'active'      => true,
            'timeout'     => 5,
            'apmVersion'  => 'v1',
            'env'         => [],
            'cookies'     => [],
            'httpClient'  => [],
        ];
    }
}
