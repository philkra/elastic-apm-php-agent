<?php

namespace PhilKra;

use PhilKra\Events\EventFactoryInterface;
use PhilKra\EventsV2\DefaultEventFactory;
use PhilKra\EventsV2\Transaction;
use PhilKra\EventsV2\Error;
use PhilKra\Middleware\ConnectorV2Interface;
use PhilKra\Middleware\HttpConnectorV2;
use PhilKra\SerializersV2\Errors;
use PhilKra\SerializersV2\Metadata;
use PhilKra\SerializersV2\Transactions;
use PhilKra\SerializersV2\Spans;
use PhilKra\Stores\ErrorsStore;
use PhilKra\Stores\TransactionsStore;
use PhilKra\Helper\Timer;
use PhilKra\Helper\Config;
use PhilKra\Exception\Transaction\DuplicateTransactionNameException;
use PhilKra\Exception\Transaction\UnknownTransactionException;

/**
 *
 * APM Agent for API v2
 *
 */
class AgentV2 implements AgentInterface
{
    /**
     * Agent Version
     *
     * @var string
     */
    const VERSION = '6.5.4';

    /**
     * Agent Name
     *
     * @var string
     */
    const NAME = 'elastic-v2-php';

    /**
     * Config Store
     *
     * @var \PhilKra\Helper\Config
     */
    private $config;

    /**
     * Transactions Store
     *
     * @var \PhilKra\Stores\TransactionsStore
     */
    private $transactionsStore;

    /**
     * Error Events Store
     *
     * @var \PhilKra\Stores\ErrorsStore
     */
    private $errorsStore;

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
     * @var EventFactoryInterface
     */
    private $eventFactory;

    /**
     * Setup the APM Agent
     *
     * @param array                 $config
     * @param array                 $sharedContext Set shared contexts such as user and tags
     * @param EventFactoryInterface $eventFactory  Alternative factory to use when creating event objects
     *
     */
    public function __construct(array $config, array $sharedContext = [], EventFactoryInterface $eventFactory = null)
    {
        // Init Agent Config
        $this->config = new Config($config);

        // Use the custom event factory or create a default one
        $this->eventFactory = $eventFactory ?? new DefaultEventFactory();

        // Init the Shared Context
        $this->sharedContext['user']   = $sharedContext['user'] ?? [];
        $this->sharedContext['custom'] = $sharedContext['custom'] ?? [];
        $this->sharedContext['tags']   = $sharedContext['tags'] ?? [];

        // Let's misuse the context to pass the environment variable and cookies
        // config to the EventBeans and the getContext method
        // @see https://github.com/philkra/elastic-apm-php-agent/issues/27
        // @see https://github.com/philkra/elastic-apm-php-agent/issues/30
        $this->sharedContext['env'] = $this->config->get('env', []);
        $this->sharedContext['cookies'] = $this->config->get('cookies', []);

        // Initialize Event Stores
        $this->transactionsStore = new TransactionsStore();
        $this->errorsStore       = new ErrorsStore();

        // Start Global Agent Timer
        $this->timer = new Timer();
        $this->timer->start();
    }

    /**
     * Start the Transaction capturing
     *
     * @throws \PhilKra\Exception\Transaction\DuplicateTransactionNameException
     *
     * @param string $name
     * @param array  $context
     *
     * @return Transaction
     */
    public function startTransaction(string $name, array $context = [], float $start = null): Transaction
    {
        // Create and Store Transaction
        $this->transactionsStore->register(
            $this->eventFactory->createTransaction($name, array_replace_recursive($this->sharedContext, $context), $start)
        );

        // Start the Transaction
        $transaction = $this->transactionsStore->fetch($name);

        if (null === $start) {
            $transaction->start();
        }

        return $transaction;
    }

    /**
     * Stop the Transaction
     *
     * @throws \PhilKra\Exception\Transaction\UnknownTransactionException
     *
     * @param string $name
     * @param array $meta, Def: []
     *
     * @return void
     */
    public function stopTransaction(string $name, array $meta = [])
    {
        $this->getTransaction($name)->setBacktraceLimit($this->config->get('backtraceLimit', 0));
        $this->getTransaction($name)->stop();
        $this->getTransaction($name)->setMeta($meta);
    }

    /**
     * Get a Transaction
     *
     * @throws \PhilKra\Exception\Transaction\UnknownTransactionException
     *
     * @param string $name
     *
     * @return Transaction
     */
    public function getTransaction(string $name)
    {
        $transaction = $this->transactionsStore->fetch($name);
        if ($transaction === null) {
            throw new UnknownTransactionException($name);
        }

        return $transaction;
    }

    /**
     * Register a Thrown Exception, Error, etc.
     *
     * @link http://php.net/manual/en/class.throwable.php
     *
     * @param \Throwable $thrown
     * @param array      $context
     *
     * @return void
     */
    public function captureThrowable(\Throwable $thrown, array $context = [])
    {
        $this->errorsStore->register(
            $this->eventFactory->createError($thrown, array_replace_recursive($this->sharedContext, $context))
        );
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
     * Send Data to V2 endpoint of APM Service
     *
     * @return bool
     */
    public function send() : bool
    {
        // Is the Agent enabled ?
        if ($this->config->get('active') === false) {
            return true;
        }

        $connector = $this->getConnector();

        //$events = (new Metadata($this->config))->jsonSerialize();
        $meta = (new Metadata($this->config))->jsonSerialize();
        $errors = (new Errors($this->config, $this->errorsStore))->jsonSerialize();
        $transactions = (new Transactions($this->config, $this->transactionsStore))->jsonSerialize();
        $spans = (new Spans($this->config, $this->transactionsStore->getAllSpans()))->jsonSerialize();

        $events = array_merge( [$meta], $transactions , $errors, $spans);

        $status =  $connector->sendEvents($events);

        if ($status === true) {
            $this->errorsStore->reset();
            $this->transactionsStore->reset();
        }


        return $status;
    }

    /**
     * @return ConnectorV2Interface
     */
    protected function getConnector()
    {
        $connector = new HttpConnectorV2($this->config);
        return $connector;
    }
}
