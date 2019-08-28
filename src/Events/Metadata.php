<?php

namespace PhilKra\Events;

use PhilKra\Agent;
use PhilKra\Helper\Config;

/**
 *
 * Metadata Event
 *
 * @link https://www.elastic.co/guide/en/apm/server/7.3/metadata-api.html
 *
 */
class Metadata extends EventBean implements \JsonSerializable
{

    /**
     * @var Config
     */
    private $config;

    /**
     * @param array $contexts
     * @param Config $config
     */
    public function __construct(array $contexts, Config $config)
    {
        parent::__construct($contexts);
        $this->config = $config;
    }

    /**
     * Generate request data
     *
     * @return array
     */
    final public function jsonSerialize(): array
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
