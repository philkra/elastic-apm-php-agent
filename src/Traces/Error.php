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

namespace PhilKra\Traces;

/**
 * APM Error
 *
 * @link https://www.elastic.co/guide/en/apm/server/6.7/error-api.html
 * @version 6.7 (v2)
 */
class Error extends Event
{

    /**
     * Serialize Error
     *
     * @return array
     */
    public function jsonSerialize() : array
    {
        $payload = [
          'error' => [
              'timestamp' => $this->getTimestamp(),

          ]
      ];

      return $payload;
    }

}
