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

namespace PhilKra\Exception\Transport;

/**
 * Unknown Transport Connector
 */
class UnsupportedConnectorTypeException extends \Exception
{
    public function __construct(string $message = '', int $code = 0, Throwable $previous = null)
    {
        parent::__construct(sprintf('No connector for "%s" is registered.', $message), $code, $previous);
    }
}
