## Distributed tracing
Distributed tracing headers are automatically handled by the transaction, the only thing you have to do is to send `elastic-traceparent-header` in request which you want to track.
```php
$parent = $agent->startTransaction('parent transaction');

$traceparent = new TraceParent(
    $parent->getTraceId(),
    $parent->getId(),
    '01'
);

$request->withHeader(
    TraceParent::HEADER_NAME,
    $traceparent->__toString()
);
```
If you are using Guzzle client, you can use `TracingGuzzleMiddleware` which will inject header for you. `transaction` is the caller who makes the request.
```php
$parent = $agent->startTransaction('parent transaction');
$middleware = new TracingGuzzleMiddleware($parent);

$stack = HandlerStack::create();
$stack->push($middleware);
$client = new Client(['handler' => $stack]);
```

Big thanks to [samuelbednarcik](https://github.com/samuelbednarcik) because the idea comes from his [elastic-apm-php-agent](https://github.com/samuelbednarcik/elastic-apm-php-agent).