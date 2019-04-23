<?php
//declare(strict_types=1);

/**
 * This file is part of the PhilKra/elastic-apm-php-agent library
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 *
 * @license http://opensource.org/licenses/MIT MIT
 * @link https://github.com/philkra/elastic-apm-php-agent GitHub
 */

namespace PhilKra;

use PhilKra\Stores\TracesStore;
use PhilKra\Factories\TracesFactory;
use PhilKra\Factories\DefaultTracesFactory;
use PhilKra\Traces\Trace;
use PhilKra\Helper\Timer;
use PhilKra\Helper\Config;
use PhilKra\Transport\Connector;
use PhilKra\Transport\TransportFactory;

/**
 *
 * APM Agent
 *
 */
class Agent
{
    /**
     * Agent Version
     *
     * @var string
     */
    public const VERSION = '6.7.0';

    /**
     * Agent Name
     *
     * @var string
     */
    public const NAME = 'apm-agent-php';

    /**
     * Config Store
     *
     * @var \PhilKra\Helper\Config
     */
    private $config;

    /**
     * Traces Store
     *
     * @var \PhilKra\Stores\TracesStore
     */
    private $traces;

    /**
     * Apm Timer
     *
     * @var \PhilKra\Helper\Timer
     */
    private $timer;

    /**
     * Common/Shared Contexts for Errors and Transactions
     *
     * @var array
     */
    private $sharedContext = [
      'user'   => [],
      'custom' => [],
      'tags'   => []
    ];

    /**
     * @var DefaultTracesFactory
     */
    private $factory;

    /**
     * Setup the APM Agent
     *
     * @param array $config
     * @param array $sharedContext Set shared contexts such as user and tags
     *
     * @return void
     */
    public function __construct(array $config, array $sharedContext = [])
    {
        // Init Agent Config
        $this->config = new Config($config);

        // Init the Traces Factory
        $this->factory = new DefaultTracesFactory($this->getConfig());

        // Init the Traces Store
        $this->traces = new TracesStore();

        // Generate Metadata Trace
        $metadata = $this->factory->newMetadata();
        $metadata->getUser()->initFromArray($sharedContext['user']);
        $this->register($metadata);

        // Init the Shared Context
        $this->sharedContext['custom'] = $sharedContext['custom'] ?? [];
        $this->sharedContext['tags']   = $sharedContext['tags'] ?? [];

        // Let's misuse the context to pass the environment variable and cookies
        // config to the EventBeans and the getContext method
        // @see https://github.com/philkra/elastic-apm-php-agent/issues/27
        // @see https://github.com/philkra/elastic-apm-php-agent/issues/30
        $this->sharedContext['env'] = $this->config->get('env', []);
        $this->sharedContext['cookies'] = $this->config->get('cookies', []);

        // Start Global Agent Timer
        $this->timer = new Timer();
        $this->timer->start();
    }

    /**
     * Inject a Custom Traces Factory
     *
     * @param TracesFactory $factory
     */
    public function setFactory(TracesFactory $factory)
    {
        $this->factory = $factory;
    }

    /**
     * Public Interface to generate Traces
     *
     * @return TracesFactory
     */
    public function factory() : TracesFactory
    {
        return $this->factory;
    }

    /**
     * Get the Agent Config
     *
     * @return \PhilKra\Helper\Config
     */
    public function getConfig() : \PhilKra\Helper\Config
    {
        return $this->config;
    }

    /**
     * Put a Trace in the Registry
     *
     * @param Trace $trace
     *
     * @return void
     */
    public function register(Trace $trace) : void
    {
        $this->traces->register($trace);
    }

    /**
     * Get the Data of the Server Information Endpoint
     *
     * @link https://www.elastic.co/guide/en/apm/server/6.7/server-info.html
     *
     * @return array
     */
    public function getServerInfo() : array
    {
        // TODO
    }

    /**
     * Send Data to APM Service
     *
     * @link https://github.com/philkra/elastic-apm-laravel/issues/22
     * @link https://github.com/philkra/elastic-apm-laravel/issues/26
     *
     * @return bool
     */
    public function send() : bool
    {
        // Is the Agent enabled ?
        if ($this->config->get('active') === false) {
            return true;
        }

        $status = true;

        // Commit the Errors
        if ($this->traces->isEmpty() === false) {
            $status = TransportFactory::new($this->config)->send($this->traces);
            if ($status === true) {
                $this->traces->reset();
            }
        }

        return $status;
    }

}
