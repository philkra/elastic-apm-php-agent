<?php
/**
 * Created by PhpStorm.
 * User: yuzhen.xie
 * Date: 2019/08/19
 * Time: 15:49
 */

namespace PhilKra\Middleware;

use Psr\Http\Message\RequestInterface;
use PhilKra\TraceParent;


class TracingGuzzleMiddleware
{
    public function __construct()
    {
    }

    /**
     * @param callable $handler
     * @return \Closure
     */
    public function __invoke(callable $handler)
    {
        return function (RequestInterface $request, array $options) use ($handler) {
            $transaction = app('request')->__apm__();
            if ($transaction !== null && $transaction->getTraceId() !== null && $transaction->getId() !== null) {
                $header = new TraceParent($transaction->getTraceId(), $transaction->getId(), '01');
                $request = $request->withHeader(TraceParent::HEADER_NAME, $header->__toString());
            }
            return $handler($request, $options);
        };
    }
}