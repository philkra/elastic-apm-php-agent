<?php

namespace PhilKra\Middleware;

use PhilKra\Agent;
use PhilKra\Events\EventBean;
use PhilKra\Helper\Config;
use Psr\Http\Client\ClientExceptionInterface;
use Psr\Http\Client\ClientInterface;
use Psr\Http\Message\RequestFactoryInterface;
use Psr\Http\Message\RequestInterface;
use Psr\Http\Message\ResponseInterface;
use Psr\Http\Message\StreamFactoryInterface;

/**
 *
 * Connector which Transmits the Data to the Endpoints
 *
 */
class Connector
{
    /**
     * @var ClientInterface
     */
    private $client;

    /**
     * @var RequestFactoryInterface
     */
    private $requestFactory;

    /**
     * @var StreamFactoryInterface
     */
    private $streamFactory;

    /**
     * @var Config
     */
    private $config;

    /**
     * @var array
     */
    private $payload = [];

    /**
     * @param ClientInterface $client
     * @param RequestFactoryInterface $requestFactory
     * @param StreamFactoryInterface $streamFactory
     * @param Config $config
     */
    public function __construct(ClientInterface $client, RequestFactoryInterface $requestFactory, StreamFactoryInterface $streamFactory, Config $config)
    {
        $this->client = $client;
        $this->requestFactory = $requestFactory;
        $this->streamFactory = $streamFactory;
        $this->config = $config;
    }

    /**
     * Is the Payload Queue populated?
     *
     * @return bool
     */
    public function isPayloadSet(): bool
    {
        return (empty($this->payload) === false);
    }

    /**
     * Put Events to the Payload Queue
     *
     * @param EventBean $event
     */
    public function putEvent(EventBean $event): void
    {
        $this->payload[] = json_encode($event);
    }

    /**
     * Commit the Events to the APM server
     *
     * @return bool
     * @throws ClientExceptionInterface
     */
    public function commit(): bool
    {
        $body = '';
        foreach ($this->payload as $line) {
            $body .= $line . "\n";
        }
        $this->payload = [];

        $request = $this->requestFactory
            ->createRequest('POST', $this->getEndpoint())
            ->withBody($this->streamFactory->createStream($body));

        $request = $this->populateRequestWithHeaders($request);

        $response = $this->client->sendRequest($request);

        return ($response->getStatusCode() >= 200 && $response->getStatusCode() < 300);
    }

    /**
     * Get the Endpoint URI of the APM Server
     *
     * @return string
     */
    private function getEndpoint(): string
    {
        return sprintf('%s/intake/v2/events', $this->config->get('serverUrl'));
    }

    /**
     * @param RequestInterface $request
     *
     * @return RequestInterface
     */
    private function populateRequestWithHeaders(RequestInterface $request): RequestInterface
    {
        foreach ($this->getRequestHeaders() as $key => $value) {
            $request = $request->withHeader($key, $value);
        }

        return $request;
    }

    /**
     * Get the Headers for the POST Request
     *
     * @return array
     */
    private function getRequestHeaders(): array
    {
        // Default Headers Set
        $headers = [
            'Content-Type' => 'application/x-ndjson',
            'User-Agent' => sprintf('elasticapm-php/%s', Agent::VERSION),
            'Accept' => 'application/json',
        ];

        // Add Secret Token to Header
        if ($this->config->get('secretToken') !== null) {
            $headers['Authorization'] = sprintf('Bearer %s', $this->config->get('secretToken'));
        }

        return $headers;
    }

    /**
     * Get the Server Informations
     *
     * @link https://www.elastic.co/guide/en/apm/server/7.3/server-info.html
     *
     * @return ResponseInterface
     * @throws ClientExceptionInterface
     */
    public function getInfo(): ResponseInterface
    {
        $request = $this->populateRequestWithHeaders(
            $this->requestFactory->createRequest(
                'GET',
                $this->config->get('serverUrl')
            )
        );

        return $this->client->sendRequest($request);
    }
}
