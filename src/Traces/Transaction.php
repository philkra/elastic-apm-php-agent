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
 * APM Transaction
 *
 * @link https://www.elastic.co/guide/en/apm/server/6.7/transaction-api.html
 * @version 6.7 (v2)
 *
 * @required ["duration", "type"]
 */
class Transaction extends Event
{
    /**
     * How long the transaction took to complete, in ms with 3 decimal points
     *
     * @var int
     */
    private $duration;

    /**
     * Keyword of specific relevance in the service's domain (eg: 'request', 'backgroundjob', etc)
     *
     * @var string
     */
    private $type;

    /**
     * Generic designation of a transaction in the scope of a single service (eg: 'GET /users/:id')
     *
     * @var string
     */
    private $name;

    /**
     * The result of the transaction. For HTTP-related transactions, this should be the status code formatted like 'HTTP 2xx'.
     *
     * @var string
     */
    private $result = null;

    /**
     * Transactions that are 'sampled' will include all available information. Transactions that are not sampled will not have 'spans' or 'context'. Defaults to true.
     *
     * @var
     */
    private $sampled = true;

    /**
     * @var array
     */
    private $spans = [];

    /**
     * @param string $type
     */
    public function __construct(string $name, string $type)
    {
        parent::__construct();

        $this->name = trim($name);
        $this->type = trim($type);
    }

    /**
     * Add a Span to the Transaction
     *
     * @param Span $span
     */
    public function addSpan(Span $span) : void
    {
        $this->spans[] = $span;
    }

    /**
     * Serialize Error
     *
     * @return array
     */
    public function jsonSerialize() : array
    {
        $payload = [
          'transaction' => [
              'id'             => $this->getId(),
              'trace_id'       => $this->getTraceId(),
        //      'parent_id'      => $this->getParentId(),
              'name'           => $this->name,
              'type'           => $this->type,
              'timestamp'      => $this->getTimer()->getNow(),
              'duration'       => $this->getDuration(),
              'sampled'        => $this->sampled,
              'span_count'     => [
                  'started' => 0,
                  'dopped'  => 0,
              ],
          ]
      ];

      return $payload;
    }

}
