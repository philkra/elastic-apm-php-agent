<?php

namespace PhilKra\SerializersV2;

use PhilKra\Agent;
use PhilKra\Helper\Config;

/**
 *
 * Class to  serializers Metadata for V2
 *
 */
class Metadata
{
    /**
     * @var \PhilKra\Helper\Config
     */
    protected $config;

    /**
     * @param Config $config
     */
    public function __construct(Config $config)
    {
        $this->config = $config;
    }

    public function jsonSerialize()
    {
        return [
            'metadata' => [
                'service' => [
                    'name'    => $this->config->get('appName'),
                    'version' => $this->config->get('appVersion'),
                    'framework' => [
                        'name' => $this->config->get('framework') ?? '',
                        'version' => $this->config->get('frameworkVersion') ?? '',
                    ],
                    'language' => [
                        'name'    => 'php',
                        'version' => phpversion()
                    ],
                    'process' => [
                        'pid' => getmypid(),
                    ],
                    'agent' => [
                        'name'    => Agent::NAME,
                        'version' => Agent::VERSION
                    ],
                    'environment' => $this->config->get('environment')
                ],
                'system' => [
                    'hostname'     => $this->config->get('hostname'),
                    'architecture' => php_uname('m'),
                    'platform'     => php_uname('s')
                ]
            ]
        ];
    }
}
