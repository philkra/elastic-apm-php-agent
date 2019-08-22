<?php
/**
 * Created by PhpStorm.
 * User: yuzhen.xie
 * Date: 2019/08/19
 * Time: 15:49
 */

namespace PhilKra\Middleware;

use PhilKra\Events\Transaction;
use Psr\Http\Message\RequestInterface;
use PhilKra\TraceParent;


class TracingGuzzleMiddleware
{
    /**
     * @var Transaction
     */
    private $transaction;

    /**
     * TracingGuzzleMiddleware constructor.
     *
     * @param Transaction|null $transaction
     */
    public function __construct(?Transaction $transaction = null)
    {
        $this->transaction = $transaction;
    }

    /**
     * @param callable $handler
     * @return \Closure
     */
    public function __invoke(callable $handler)
    {
        return function (RequestInterface $request, array $options) use ($handler) {
            if ($this->transaction !== null && $this->transaction->getTraceId() !== null && $this->transaction->getId() !== null) {
                $header = new TraceParent($this->transaction->getTraceId(), $this->transaction->getId(), '01');
                $request = $request->withHeader(TraceParent::HEADER_NAME, $header->__toString());
            }
            return $handler($request, $options);
        };
    }
}