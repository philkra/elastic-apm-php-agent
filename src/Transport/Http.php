<?php
/**
 * This file is part of the PhilKra/elastic-apm-php-agent library
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 *
 * @license http://opensource.org/licenses/MIT MIT
 * @link https://github.com/philkra/elastic-apm-php-agent GitHub
 */

namespace PhilKra\Transport;

use PhilKra\Agent;
use \PhilKra\Helper\Config;
use PhilKra\Stores\TracesStore;
use GuzzleHttp\Psr7\Request;
use GuzzleHttp\Client;

/**
 *
 * Http Connector to the APM Server Endpoints
 *
 */
class Http extends Connector
{

    /**
     * @var \GuzzleHttp\Client
     */
    private $client;

    /**
     * @param \PhilKra\Helper\Config $config
     * @param Client|null $client
     */
    public function __construct(Config $config, Client $client = null)
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

        $this->client = new Client($this->config->get('transport.config'));
    }

    /**
     * {@inheritdoc}
     */
    public function send(TracesStore $store) : bool
    {
        $endpoint = sprintf('%s/intake/v2/events', $this->config->get('transport.host'));
        var_dump($store->toNdJson());

        $request = new Request(
            'POST',
            $endpoint,
            $this->getRequestHeaders(),
            $store->toNdJson()
        );

        $response = $this->client->send($request);
        return ($response->getStatusCode() >= 200 && $response->getStatusCode() < 300);
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
            'User-Agent'   => sprintf('apm-agent-php/%s', Agent::VERSION),
        ];

        // Add Secret Token to Header
        if ($this->config->get('secretToken') !== null) {
            $headers['Authorization'] = sprintf('Bearer %s', $this->config->get('secretToken'));
        }

        return $headers;
    }

}
