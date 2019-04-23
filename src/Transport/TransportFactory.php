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

use PhilKra\Helper\Config;
use PhilKra\Exception\Transport\UnsupportedConnectorTypeException;

/**
 *
 * Connector Factory for the creation of the Agent to Server Connection
 *
 */
final class TransportFactory
{

    /**
     * Create Connector Instance based on the Config settings
     *
     * @param \PhilKra\Helper\Config $config
     *
     * @throws UnsupportedConnectorTypeException
     *
     * @return Connector
     */
    public static function new(Config $config) : Connector
    {
        // Read the Config
        $transport = $config->get('transport');
        $method    = mb_strtolower($transport['method']);

        // Http Transport Handler
        if ($method === 'http') {
            return new Http($config);
        }

        // TODO -- via Socket
        // TODO -- put to message queue

        // Ooops!?!
        throw new UnsupportedConnectorTypeException($method);
    }

}
