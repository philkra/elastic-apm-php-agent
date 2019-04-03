<?php

namespace PhilKra\EventsV2;
use Ramsey\Uuid\Uuid;


/**
 *
 * Abstract Transaction class for all inheriting Transactions
 *
 * @link https://www.elastic.co/guide/en/apm/server/master/transaction-api.html
 *
 */
class Transaction extends \PhilKra\Events\Transaction
{

    protected $trace_id = '';

    /**
    * Create the Transaction
    *
    * @param string $name
    * @param array $contexts
    */
    public function __construct(string $name, array $contexts, $start = null)
    {
        parent::__construct($name, $contexts, $start);
        $this->id = substr(Uuid::uuid4()->getHex(), 0, 8);
        $this->trace_id = substr(Uuid::uuid4()->getHex(), 0, 16);
        // Get UTC timestamp of Now
        $timestamp = \DateTime::createFromFormat('U.u', sprintf('%.6F', microtime(true)));
        $timestamp->setTimeZone(new \DateTimeZone('UTC'));
        $this->timestamp = $timestamp->format('U');

        $this->summary = array_merge($this->summary, [
            'started_spans' => 0, // Number of correlated spans that are recorded.
            'dropped_spans' => 0  // Number of spans that have been dropped by the agent recording the transaction.
        ]);


    }

    public function getTraceId()
    {
        return $this->trace_id;
    }

    /**
     * Stop the Transaction
     *
     * @param integer|null $duration
     *
     * @return void
     */
    public function stop(int $duration = null)
    {

        parent::stop($duration);
        $this->summary['started_spans'] = count($this->spans);
        $this->summary['dropped_spans'] = 0; //no sampling implemented yet

    }


    /**
     * Get the spans from the transaction
     *
     * @return array
     */
    public function getSpans(): array
    {

        $spans = [];
        foreach ($this->spans as $span) {

            if (empty($span['id']) || strlen($span['id']) != 8) {
                $span['id'] = substr(Uuid::uuid4()->getHex(), 0, 8);
            }

            if (empty($span['transaction_id']) ) {
                $span['transaction_id'] =$this->id;
            }

            if (empty($span['trace_id']) ) {
                $span['trace_id'] =$this->trace_id;
            }
            if (empty($span['parent_id']) ) {
                $span['parent_id'] = substr(Uuid::uuid4()->getHex(), 0, 16);
            }

            $spans[] = $span;

        }
        return $spans;


    }

    /**
    * Serialize Transaction Event
    *
    * @return array
    */
    public function jsonSerialize() : array
    {
        return [
            'transaction' =>
                [
                    'context'   => $this->getContext(),
                    'duration'  => $this->summary['duration'],//"type": "number",  "description": "How long the transaction took to complete, in ms with 3 decimal points"
                    'name'      => $this->getTransactionName(),// "type": ["string","null"], "description": "Generic designation of a transaction in the scope of a single service (eg: 'GET /users/:id')", "maxLength": 1024
                    'result'    => $this->getMetaResult(), // "type": ["string", "null"], "description": "The result of the transaction. For HTTP-related transactions, this should be the status code formatted like 'HTTP 2xx'.", "maxLength": 1024
                    'type'      => $this->getMetaType(),
                    // "marks" ; "type": ["object", "null"], "description": "A mark captures the timing of a significant event during the lifetime of a transaction. Marks are organized into groups and can be set by the user or the agent.", "patternProperties": { "^[^.*\"]*$": { "$ref": "mark.json"} }, "additionalProperties": false }
                    // "sampled": { "type": ["boolean", "null"], "description": "Transactions that are 'sampled' will include all available information. Transactions that are not sampled will not have 'spans' or 'context'. Defaults to true." }

                    'timestamp' => (int) ($this->getTimestamp()),

                    'id'        => $this->getId(), // "type": "string", "description": "Hex encoded 64 random bits ID of the transaction.", "maxLength": 1024
                    'trace_id'  => $this->getTraceId(), // "type": "string", "description": "Hex encoded 128 random bits ID of the correlated trace.", "maxLength": 1024
                    'span_count'  => [
                        'started' =>   $this->summary['started_spans'],
                        'dropped' =>   $this->summary['dropped_spans'],

                    ], // "type": "object", "properties": { "started": {"type": "integer","description": "Number of correlated spans that are recorded."},"dropped": {"type": ["integer","null"],"description": "Number of spans that have been dropped by the agent recording the transaction."}},
                ]
        ];
    }
}
