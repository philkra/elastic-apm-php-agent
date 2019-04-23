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

/**
 *
 * Connector which Transmits the Data to the Endpoints
 *
 */
abstract class Connector
{

    /**
     * Agent Config
     *
     * @var \PhilKra\Helper\Config
     */
    private $config;

    /**
     * @param \PhilKra\Helper\Config $config
     */
    public function __construct(Config $config)
    {
        $this->config = $config;
    }

    /**
     * Transmit the traces to the APM Server
     *
     * @param PhilKra\Stores\TracesStore $store
     *
     * @return bool
     */
    public abstract function send(TracesStore $store) : bool;

}
