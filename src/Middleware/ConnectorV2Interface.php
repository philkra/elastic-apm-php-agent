<?php
/**
 *
 * @author david latotzky <david.latotzky@rocket-internet.de
 *
 */
namespace PhilKra\Middleware;


/**
 *
 * Connector which Transmits the Data to the Endpoints
 *
 */
interface ConnectorV2Interface
{
    /**
     * @param \PhilKra\Helper\Config $config
     */
    public function __construct(\PhilKra\Helper\Config $config);

    /**
     * Push the Transactions to APM Server
     *
     * @param array $events array of json_encode-able objects, each represignt an event.
     *
     * @return bool
     */
    public function sendEvents(array $events) : bool;
}