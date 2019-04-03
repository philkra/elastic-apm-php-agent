<?php
/**
 *
 * @author david latotzky <david.latotzky@rocket-internet.de
 *
 */
namespace PhilKra;

use PhilKra\Events\EventFactoryInterface;
use PhilKra\Events\Transaction;


/**
 *
 * Interface for APM Agents
 *
 *
 */
interface AgentInterface
{
    /**
     * Setup the APM Agent
     *
     * @param array $config
     * @param array $sharedContext Set shared contexts such as user and tags
     * @param EventFactoryInterface $eventFactory Alternative factory to use when creating event objects
     *
     * @return void
     */
    public function __construct(array $config, array $sharedContext = [], EventFactoryInterface $eventFactory = null);

    /**
     * Start the Transaction capturing
     *
     * @throws \PhilKra\Exception\Transaction\DuplicateTransactionNameException
     *
     * @param string $name
     * @param array $context
     *
     * @return Transaction
     */
    public function startTransaction(string $name, array $context = [], float $start = null) : Transaction;

    /**
     * Stop the Transaction
     *
     * @throws \PhilKra\Exception\Transaction\UnknownTransactionException
     *
     * @param string $name
     * @param array $meta , Def: []
     *
     * @return void
     */
    public function stopTransaction(string $name, array $meta = []);

    /**
     * Get a Transaction
     *
     * @throws \PhilKra\Exception\Transaction\UnknownTransactionException
     *
     * @param string $name
     *
     * @return void
     */
    public function getTransaction(string $name);

    /**
     * Register a Thrown Exception, Error, etc.
     *
     * @link http://php.net/manual/en/class.throwable.php
     *
     * @param \Throwable $thrown
     * @param array $context
     *
     * @return void
     */
    public function captureThrowable(\Throwable $thrown, array $context = []);

    /**
     * Get the Agent Config
     *
     * @return \PhilKra\Helper\Config
     */
    public function getConfig() : \PhilKra\Helper\Config;

    /**
     * Send Data to APM Service
     *
     * @link https://github.com/philkra/elastic-apm-laravel/issues/22
     * @link https://github.com/philkra/elastic-apm-laravel/issues/26
     *
     * @return bool
     */
    public function send() : bool;
}