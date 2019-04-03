<?php

namespace PhilKra\Middleware;

use PhilKra\Agent;
use GuzzleHttp\Psr7\Request;
use GuzzleHttp\Client;

/**
 *
 * Connector which Transmits the Data to the Endpoints
 *
 */
class HttpConnectorV2 implements ConnectorV2Interface
{
    /**
     * Agent Config
     *
     * @var \PhilKra\Helper\Config
     */
    private $config;

    /**
     * @var \GuzzleHttp\Client
     */
    private $client;

    /**
     * @param \PhilKra\Helper\Config $config
     */
    public function __construct(\PhilKra\Helper\Config $config)
    {
        $this->config = $config;

        $this->configureHttpClient();
    }

    /**
     * Create and configure the HTTP client
     *
     * @return void
     */
    private function configureHttpClient()
    {
        $httpClientDefaults = [
            'timeout' => $this->config->get('timeout'),
        ];

        $httpClientConfig = $this->config->get('httpClient') ?? [];

        $this->client = new Client(array_merge($httpClientDefaults, $httpClientConfig));
    }

    /**
     * Push the Transactions to APM Server
     *
     * @param array $events
     *
     * @return bool
     */
    public function sendEvents(array $events) : bool
    {
        $body =  implode("\n", array_map(function ($a) { return json_encode($a); }, $events));

        $request = new Request(
            'POST',
            $this->getEndpoint(),
            $this->getRequestHeaders(),
            $body
        );

        $response = $this->client->send($request);
        return ($response->getStatusCode() >= 200 && $response->getStatusCode() < 300);
    }


    /**
     * Get the Endpoint URI of the APM Server
     *
     *
     * @return string
     */
    private function getEndpoint() : string
    {
        return sprintf('%s/intake/v2/events',$this->config->get('serverUrl')
        );
    }

    /**
     * Get the Headers for the POST Request
     *
     * @return array
     */
    private function getRequestHeaders() : array
    {
        // Default Headers Set
        $headers = [
            'Content-Type' => 'application/x-ndjson',
            'User-Agent'   => sprintf('elasticapm-php/%s', Agent::VERSION),
        ];

        // Add Secret Token to Header
        if ($this->config->get('secretToken') !== null) {
            $headers['Authorization'] = sprintf('Bearer %s', $this->config->get('secretToken'));
        }

        return $headers;
    }
}
