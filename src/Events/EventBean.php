<?php

namespace PhilKra\Events;

use PhilKra\Helper\Encoding;

/**
 *
 * EventBean for occurring events such as Exceptions or Transactions
 *
 */
class EventBean
{
    /**
     * Bit Size of ID's
     */
    const
        EVENT_ID_BITS  = 64,
        TRACE_ID_BITS = 128;

    /**
     * Event Id
     *
     * @var string
     */
    private $id;

    /**
     * Id of the whole trace forest and is used to uniquely identify a distributed trace through a system
     * @link https://www.w3.org/TR/trace-context/#trace-id
     *
     * @var string
     */
    private $traceId;

    /**
     * Id of parent span or parent transaction
     *
     * @link https://www.w3.org/TR/trace-context/#parent-id
     *
     * @var string
     */
    private $parentId = null;

    /**
     * Error occurred on Timestamp
     *
     * @var float
     */
    private $timestamp;

    /**
     * Event Metadata
     *
     * @var array
     */
    private $meta = [
        'result' => 200,
        'type'   => 'generic'
    ];

    /**
     * Extended Contexts such as Custom and/or User
     *
     * @var array
     */
    private $contexts = [
        'request'  => [],
        'user'     => [],
        'custom'   => [],
        'env'      => [],
        'tags'     => [],
        'response' => [
            'finished'     => true,
            'headers_sent' => true,
            'status_code'  => 200,
        ],
    ];

    /**
     * Init the Event with the Timestamp and UUID
     *
     * @link https://github.com/philkra/elastic-apm-php-agent/issues/3
     *
     * @param array $contexts
     * @param ?Transaction $parent
     */
    public function __construct(array $contexts, ?Transaction $parent = null)
    {
        // Generate Random Event Id
        $this->id = self::generateRandomBitsInHex(self::EVENT_ID_BITS);

        // Merge Initial Context
        $this->contexts = array_merge($this->contexts, $contexts);

        // Get current Unix timestamp with seconds
        $this->timestamp = round(microtime(true) * 1000000);

        // Set Parent Transaction
        if ($parent !== null) {
            $this->setParent($parent);
        }
    }

    /**
     * Get the Event Id
     *
     * @return string
     */
    public function getId() : string
    {
        return $this->id;
    }

    /**
     * Get the Trace Id
     *
     * @return string $traceId
     */
    public function getTraceId() : ?string
    {
        return $this->traceId;
    }

    /**
     * Set the Trace Id
     *
     * @param string $traceId
     */
    final public function setTraceId(string $traceId)
    {
        $this->traceId = $traceId;
    }

    /**
     * Set the Parent Id
     *
     * @param string $parentId
     */
    final public function setParentId(string $parentId)
    {
        $this->parentId = $parentId;
    }

    /**
     * Get the Parent Id
     *
     * @return string
     */
    final public function getParentId() : ?string
    {
        return $this->parentId;
    }

    /**
     * Get the Offset between the Parent's timestamp and this Event's
     *
     * @return int
     */
    final public function getParentTimestampOffset(): ?int
    {
        return $this->parentTimestampOffset;
    }

    /**
     * Get the Event's Timestamp
     *
     * @return int
     */
    public function getTimestamp() : int
    {
        return $this->timestamp;
    }

    /**
     * Set the Parent Id and Trace Id based on the Parent Event
     *
     * @link https://www.elastic.co/guide/en/apm/server/current/transaction-api.html
     *
     * @param EventBean $parent
     */
    public function setParent(EventBean $parent)
    {
        $this->setParentId($parent->getId());
        $this->setTraceId($parent->getTraceId());
    }

    /**
     * Set the Transaction Meta data
     *
     * @param array $meta
     *
     * @return void
     */
    final public function setMeta(array $meta)
    {
        $this->meta = array_merge($this->meta, $meta);
    }

    /**
     * Set Meta data of User Context
     *
     * @param array $userContext
     */
    final public function setUserContext(array $userContext)
    {
        $this->contexts['user'] = array_merge($this->contexts['user'], $userContext);
    }

    /**
     * Set custom Meta data for the Transaction in Context
     *
     * @param array $customContext
     */
    final public function setCustomContext(array $customContext)
    {
        $this->contexts['custom'] = array_merge($this->contexts['custom'], $customContext);
    }

    /**
     * Set Transaction Response
     *
     * @param array $response
     */
    final public function setResponse(array $response)
    {
        $this->contexts['response'] = array_merge($this->contexts['response'], $response);
    }

    /**
     * Set Tags for this Transaction
     *
     * @param array $tags
     */
    final public function setTags(array $tags)
    {
        $this->contexts['tags'] = array_merge($this->contexts['tags'], $tags);
    }

    /**
     * Set Transaction Request
     *
     * @param array $request
     */
    final public function setRequest(array $request)
    {
        $this->contexts['request'] = array_merge($this->contexts['request'], $request);
    }

    /**
     * Generate request data
     *
     * @return array
     */
    final public function generateRequest(): array
    {
        $headers = getallheaders();
        $http_or_https = isset($_SERVER['HTTPS']) ? 'https' : 'http';

        // Build Context Stub
        $SERVER_PROTOCOL = $_SERVER['SERVER_PROTOCOL'] ?? '';
        $remote_address = $_SERVER['REMOTE_ADDR'] ?? '';
        if (array_key_exists('HTTP_X_FORWARDED_FOR', $_SERVER) === true) {
            $remote_address = $_SERVER['HTTP_X_FORWARDED_FOR'];
        }

        return [
            'http_version' => substr($SERVER_PROTOCOL, strpos($SERVER_PROTOCOL, '/')),
            'method'       => $_SERVER['REQUEST_METHOD'] ?? 'cli',
            'socket'       => [
                'remote_address' => $remote_address,
                'encrypted'      => isset($_SERVER['HTTPS'])
            ],
            'response' => $this->contexts['response'],
            'url'          => [
                'protocol' => $http_or_https,
                'hostname' => Encoding::keywordField($_SERVER['SERVER_NAME'] ?? ''),
                'port'     => $_SERVER['SERVER_PORT'] ?? null,
                'pathname' => Encoding::keywordField($_SERVER['SCRIPT_NAME'] ?? ''),
                'search'   => Encoding::keywordField('?' . (($_SERVER['QUERY_STRING'] ?? '') ?? '')),
                'full' => Encoding::keywordField(isset($_SERVER['HTTP_HOST']) ? $http_or_https . '://' . $_SERVER['HTTP_HOST'] . $_SERVER['REQUEST_URI'] : ''),
            ],
            'headers' => [
                'user-agent' => $headers['User-Agent'] ?? '',
                'cookie'     => $this->getCookieHeader($headers['Cookie'] ?? ''),
            ],
            'env' => (object)$this->getEnv(),
            'cookies' => (object)$this->getCookies(),
        ];
    }

    /**
     * Generate random bits in hexadecimal representation
     *
     * @param int $bits
     * @return string
     * @throws \Exception
     */
    final protected function generateRandomBitsInHex(int $bits): string
    {
        return bin2hex(random_bytes($bits/8));
    }

    /**
     * Get Type defined in Meta
     *
     * @return string
     */
    final protected function getMetaType() : string
    {
        return $this->meta['type'];
    }

    /**
     * Get the Result of the Event from the Meta store
     *
     * @return string
     */
    final protected function getMetaResult() : string
    {
        return (string)$this->meta['result'];
    }

    /**
     * Get the Environment Variables
     *
     * @link http://php.net/manual/en/reserved.variables.server.php
     * @link https://github.com/philkra/elastic-apm-php-agent/issues/27
     * @link https://github.com/philkra/elastic-apm-php-agent/issues/54
     *
     * @return array
     */
    final protected function getEnv() : array
    {
        $envMask = $this->contexts['env'];
        $env = empty($envMask)
            ? $_SERVER
            : array_intersect_key($_SERVER, array_flip($envMask));

        return $env;
    }

    /**
     * Get the cookies
     *
     * @link https://github.com/philkra/elastic-apm-php-agent/issues/30
     * @link https://github.com/philkra/elastic-apm-php-agent/issues/54
     *
     * @return array
     */
    final protected function getCookies() : array
    {
        $cookieMask = $this->contexts['cookies'] ?? [];
        return empty($cookieMask)
            ? $_COOKIE
            : array_intersect_key($_COOKIE, array_flip($cookieMask));
    }

    /**
     * Get the cookie header
     *
     * @link https://github.com/philkra/elastic-apm-php-agent/issues/30
     *
     * @return string
     */
    final protected function getCookieHeader(string $cookieHeader) : string
    {
        $cookieMask = $this->contexts['cookies'] ?? [];

        // Returns an empty string if cookies are masked.
        return empty($cookieMask) ? $cookieHeader : '';
    }

    /**
     * Get the Events Context
     *
     * @link https://www.elastic.co/guide/en/apm/server/current/transaction-api.html#transaction-context-schema
     *
     * @return array
     */
    final protected function getContext() : array
    {
        $context = [
            'request' => empty($this->contexts['request']) ? $this->generateRequest() : $this->contexts['request']
        ];

        // Add User Context
        if (empty($this->contexts['user']) === false) {
            $context['user'] = $this->contexts['user'];
        }

        // Add Custom Context
        if (empty($this->contexts['custom']) === false) {
            $context['custom'] = $this->contexts['custom'];
        }

        // Add Tags Context
        if (empty($this->contexts['tags']) === false) {
            $context['tags'] = $this->contexts['tags'];
        }

        return $context;
    }
}
