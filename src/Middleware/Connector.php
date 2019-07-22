<?php

namespace PhilKra\Middleware;

use PhilKra\Agent;
use PhilKra\Stores\ErrorsStore;
use PhilKra\Stores\TransactionsStore;
use PhilKra\Serializers\Errors;
use PhilKra\Serializers\Transactions;
use GuzzleHttp\Client;
use PhilKra\Serializers\Entity;

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
        $response = $this->client->post($this->getEndpoint(), [
            'headers' => $this->getRequestHeaders(),
            'body' => $this->buildEventPayload(new Transactions($this->config, $store), 'transactions', 'transaction')
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
        $response = $this->client->post($this->getEndpoint(), [
            'headers' => $this->getRequestHeaders(),
            'body' => $this->buildEventPayload(new Errors($this->config, $store), 'errors', 'error')
        ]);

        return ($response->getStatusCode() >= 200 && $response->getStatusCode() < 300);
    }

    /**
     * Transform the incoming entity to v2 ndjson style
     *
     * @param PhilKra\Serializers\Entity $entity
     * @param string $extractEvent the old event key
     * @param string $as the new event key
     *
     * @return string
     */
    private function buildEventPayload(Entity $entity, string $extractEvent, string $as): string {
        $data = $entity->jsonSerialize();

        $body = json_encode(['metadata' => $data['metadata']]);

        foreach ( $data[$extractEvent]->list() as $item ) {
            $obj = $item->jsonSerialize();
            $spans = $obj['spans'] ?? [];

            unset($obj['spans']);

            $body .= "\n" . json_encode([$as => $item]);

            if ( !empty($spans) ) {
                foreach ( $spans as $i => $span ) {
                    $span = array_merge($span, [
                        'id' => $obj['id'] . '-' . $i,
                        'parent_id' => $obj['id'],
                        'transaction_id' => $obj['id'],
                        'trace_id' => $obj['trace_id'],
                    ]);
                    $body .= "\n" . json_encode(['span' => $span]);
                }
            }
        }

        return $body;
    }

    /**
     * Get the Endpoint URI of the APM Server
     *
     * @param string $endpoint
     *
     * @return string
     */
    private function getEndpoint() : string
    {
        return sprintf(
            '%s/intake/v2/%s',
            $this->config->get('serverUrl'),
            'events'
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
            'Accept'       => 'application/json'
        ];

        // Add Secret Token to Header
        if ($this->config->get('secretToken') !== null) {
            $headers['Authorization'] = sprintf('Bearer %s', $this->config->get('secretToken'));
        }

        return $headers;
    }
}
