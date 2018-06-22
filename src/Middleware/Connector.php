<?php

namespace PhilKra\Middleware;

use PhilKra\Agent;
use PhilKra\Stores\ErrorsStore;
use PhilKra\Stores\TransactionsStore;
use PhilKra\Serializers\Errors;
use PhilKra\Serializers\Transactions;
use GuzzleHttp\Psr7\Request;
use GuzzleHttp\Client;

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
        $this->client = new Client();
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
        $request = new Request(
            'POST',
            $this->getEndpoint('transactions'),
            $this->getRequestHeaders(),
            json_encode(new Transactions($this->config, $store))
        );

        $response = $this->client->send($request);
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
        $request = new Request(
            'POST',
            $this->getEndpoint('errors'),
            $this->getRequestHeaders(),
            json_encode(new Errors($this->config, $store))
        );

        $response = $this->client->send($request);
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
            $headers['Authorization'] = sprintf('Bearer %s', $this->config['secretToken']);
        }

        return $headers;
    }
}
