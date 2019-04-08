<?php

namespace PhilKra\Middleware;

use PhilKra\Agent;
use PhilKra\Exception\Serializers\UnsupportedApmVersionException;
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
     * @param Client|null $client
     */
    public function __construct(\PhilKra\Helper\Config $config, Client $client = null)
    {
        $this->config = $config;
        $this->client = $client;

        $this->configureHttpClient();
    }

    /**
     * Create and configure the HTTP client
     *
     * @return void
     */
    private function configureHttpClient()
    {
        if (null !== $this->client) {
            return;
        }

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
        // TODO in the long run, the Connector should not be responsible for
        // understanding version specific send requirements.
        if ($this->config->useVersion1()) {
            $request = new Request(
                'POST',
                $this->getEndpoint('transactions'),
                $this->getRequestHeaders(),
                json_encode(new Transactions($this->config, $store))
            );

            $response = $this->client->send($request);
            return ($response->getStatusCode() >= 200 && $response->getStatusCode() < 300);
        }

        if ($this->config->useVersion2()) {
            return $this->sendTransactionsWithVersion2($store);
        }

        throw new UnsupportedApmVersionException($this->config->apmVersion());
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
            $headers['Authorization'] = sprintf('Bearer %s', $this->config->get('secretToken'));
        }

        return $headers;
    }

    private function sendTransactionsWithVersion2(TransactionsStore $store): bool
    {
        // TODO handling of individual request failures
        $success = true;

        // TODO we should serialize individual transactions
        // This is a hack to make the serializer produce the required v2 structure, but
        // have the connector send the transactions individually
        $transactions = json_decode(json_encode(new Transactions($this->config, $store)), true);
        foreach ($transactions as $transaction) {
            $request = new Request(
                'POST',
                $this->getEndpoint('transactions'),
                $this->getRequestHeaders(),
                json_encode($transaction)
            );

            $response = $this->client->send($request);

            // This will use the last response status, which may ok in most cases, assuming
            // they all succeed or fail, but is not a good long term solution.
            $success = ($response->getStatusCode() >= 200 && $response->getStatusCode() < 300);
        }

        return $success;
    }
}
