<?php

namespace PhilKra;

use PhilKra\Stores\ErrorsStore;
use PhilKra\Stores\TransactionsStore;
use PhilKra\Events\Transaction;
use PhilKra\Events\Error;
use PhilKra\Helper\Timer;
use PhilKra\Helper\Config;
use PhilKra\Middleware\Connector;
use PhilKra\Exception\Transaction\DuplicateTransactionNameException;
use PhilKra\Exception\Transaction\UnknownTransactionException;

/**
 *
 * APM Agent
 *
 * @link https://www.elastic.co/guide/en/apm/server/master/transaction-api.html
 *
 */
class Agent
{
    /**
     * Agent Version
     *
     * @var string
     */
    const VERSION = '6.3.2';

    /**
     * Agent Name
     *
     * @var string
     */
    const NAME = 'elastic-php';

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
     * Setup the APM Agent
     *
     * @param array $config
     * @param array $sharedContext  Set shared contexts such as user and tags
     *
     * @return void
     */
    public function __construct(array $config, array $sharedContext = [])
    {
        // Init Agent Config
        $this->config = new Config($config);

        // Init the Shared Context
        $this->sharedContext['user']   = $sharedContext['user'] ?? [];
        $this->sharedContext['custom'] = $sharedContext['custom'] ?? [];
        $this->sharedContext['tags']   = $sharedContext['tags'] ?? [];

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
     *
     * @return Transaction
     */
    public function startTransaction(string $name): Transaction
    {
        // Create and Store Transaction
        $this->transactionsStore->register(new Transaction($name, $this->sharedContext));

        // Start the Transaction
        $transaction = $this->transactionsStore->fetch($name);
        $transaction->start();
    
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
     * @return void
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
     *
     * @return void
     */
    public function captureThrowable(\Throwable $thrown)
    {
        $this->errorsStore->register(new Error($thrown, $this->sharedContext));
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
     * Send Data to APM Service
     *
     * @return bool
     */
    public function send() : bool
    {
        // Is the Agent enabled ?
        if ($this->config->get('active') === false) {
            return false;
        }

        $connector = new Connector($this->config);
        $status = true;

        // Commit the Errors
        if ($this->errorsStore->isEmpty() === false) {
            $status = $status && $connector->sendErrors($this->errorsStore);
        }

        // Commit the Transactions
        if ($this->transactionsStore->isEmpty() === false) {
            $status = $status && $connector->sendTransactions($this->transactionsStore);
        }

        return $status;
    }
}
