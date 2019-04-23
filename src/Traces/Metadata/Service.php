<?php

namespace PhilKra\Traces\Metadata;

use PhilKra\Agent;
use PhilKra\Helper\Config;
use PhilKra\Traces\Trace;

/**
 * APM Metadata
 *
 * @link https://www.elastic.co/guide/en/apm/server/6.7/metadata-api.html#metadata-service-schema
 * @version 6.7 (v2)
 */
class Service implements Trace
{

    /**
     * @var Config
     */
    private $config;

    /**
     * @param Config $config
     */
    public function __construct(Config $config)
    {
        $this->config = $config;
    }

    /**
     * Serialize Metadata Trace
     *
     * @throws Exception
     *
     * @return array
     */
    public function jsonSerialize() : array
    {
        // Ensure required service name is set
        if($this->config->get('name') === null) {
            throw new \Exception('Mandatory service name not set.');
        }

        // Build Payload to serialize
        $payload = [
            'name' => $this->config->get('name'),
            'version' => $this->config->get('version'),
            'agent' => [
                'name'    => Agent::NAME,
                'version' => Agent::VERSION,
            ],
            'language' => [
                'name'    => 'php',
                'version' => phpversion(),
            ],
            'environment' => $this->config->get('environment'),
        ];
        if(empty($this->config->get('framework', [])) === false) {
            $payload['framework'] = $this->config->get('framework');
        }

        return $payload;
    }

}
