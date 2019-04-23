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

namespace PhilKra\Traces\SpanContexts;

/**
 *
 * Http Context for a Span
 *
 * @link https://www.elastic.co/guide/en/apm/server/6.7/span-api.html
 *
 */
final class Http extends SpanContext implements \JsonSerializable {

    /**
     * The raw url of the correlating http request.
     *
     * @var string
     */
    private $url;

    /**
     * The status code of the http request.
     *
     * @var int
     */
    private $statusCode;

    /**
     * The method of the http request.
     *
     * @var string
     */
    private $method;

    /**
     * @param string $url
     * @param int $statusCode
     * @param string $method
     */
    public function __construct(string $url, int $statusCode, string $method)
    {
        $this->url = trim($url);
        $this->statusCode = $statusCode;
        $this->method = trim($method);
    }

    /**
     * @return array
     */
    public function jsonSerialize() : array
    {
        return [
            'url' => $this->url,
            'status_code' => $this->statusCode,
            'method' => $this->method,
        ];
    }

}
