<?php

namespace PhilKra\Middleware;

use PhilKra\Agent;
use GuzzleHttp\Client;
use PhilKra\Serializers\Errors;
use PhilKra\Stores\ErrorsStore;
use PhilKra\Serializers\Transactions;
use PhilKra\Stores\TransactionsStore;

/**
 *
 * Connector which Transmits the Data to the Endpoints
 *
 */
class Connector
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
     * @param \PhilKra\Stores\TransactionsStore $store
     *
     * @return bool
     */
    public function sendTransactions(TransactionsStore $store) : bool
    {
        $response = $this->client->post($this->getEndpoint('transactions'), [
            'headers' => $this->getRequestHeaders(),
            'json' => new Transactions($this->config, $store)
        ]);
        return ($response->getStatusCode() >= 200 && $response->getStatusCode() < 300);
    }

    /**
     * Push the Errors to APM Server
     *
     * @param \PhilKra\Stores\ErrorsStore $store
     *
     * @return bool
     */
    public function sendErrors(ErrorsStore $store) : bool
    {
        $response = $this->client->post($this->getEndpoint('errors'), [
            'headers' => $this->getRequestHeaders(),
            'json' => new Errors($this->config, $store)
        ]);

        return ($response->getStatusCode() >= 200 && $response->getStatusCode() < 300);
    }

    /**
     * Get the Endpoint URI of the APM Server
     *
     * @param string $endpoint
     *
     * @return string
     */
    private function getEndpoint(string $endpoint) : string
    {
        return sprintf(
            '%s/%s/%s',
            $this->config->get('serverUrl'),
            $this->config->get('apmVersion'),
            $endpoint
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
            'Content-Type' => 'application/json',
            'User-Agent'   => sprintf('elasticapm-php/%s', Agent::VERSION),
        ];

        // Add Secret Token to Header
        if ($this->config->get('secretToken') !== null) {
            $headers['Authorization'] = sprintf('Bearer %s', $this->config->get('secretToken'));
        }

        return $headers;
    }
}
