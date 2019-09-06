# Distributed tracing
Distributed tracing headers are automatically captured by transactions.
Elastic's `elastic-apm-traceparent` and W3C's `traceparent` headers are both supported.

This example illustrates the forward propagtion of the tracing Id, by showing how the invoked service queries another service.

**TL:DR** Passing the distributed tracing id to another service, you need to add the header `elastic-apm-traceparent` with the value of `getDistributedTracing()` of a `Span` or a `Transaction`.

## Screenshots
![Dashboard](https://github.com/philkra/elastic-apm-php-agent/blob/master/docs/examples/blob/dt_dashboard.png "Distributed Tracing Dashboard")

## Example Code
```php
// Setup Agent
$config = [
    'appName'    => 'examples',
    'appVersion' => '1.0.0',
];
$agent = new Agent($config);

// Wrap everything in a Parent transaction
$parent = $agent->startTransaction('GET /data/12345');
$spanCache = $agent->factory()->newSpan('DB User Lookup', $parent);
$spanCache->setType('db.redis.query');
$spanCache->start();

// do some db.mysql.query action ..
usleep(rand(250, 450));

$spanCache->stop();
$spanCache->setContext(['db' => [
    'instance'  => 'redis01.example.foo',
    'statement' => 'GET data_12345',
]]);
$agent->putEvent($spanCache);

// Query microservice with Traceparent Header
$spanHttp = $agent->factory()->newSpan('Query DataStore Service', $parent);
$spanHttp->setType('external.http');
$spanHttp->start();

$url = 'http://127.0.0.1:5001';
$curl = curl_init();
curl_setopt_array($curl, [
    CURLOPT_RETURNTRANSFER => 1,
    CURLOPT_URL            => $url,
    CURLOPT_HTTPHEADER     => [
        sprintf('%s: %s', DistributedTracing::HEADER_NAME, $spanHttp->getDistributedTracing()),
    ],
]);
$resp = curl_exec($curl);
//$info = curl_getinfo($curl);

$spanHttp->stop();
$spanHttp->setContext(['http' => [
    'instance'  => $url,
    'statement' => 'GET /',
]]);
$agent->putEvent($spanHttp);

// do something with the file
$span = $agent->factory()->newSpan('do something', $parent);
$span->start();
usleep(rand(2500, 3500));
$span->stop();
$agent->putEvent($span);

$agent->stopTransaction($parent->getTransactionName());
```

Big thanks to [samuelbednarcik](https://github.com/samuelbednarcik) because the idea comes from his [elastic-apm-php-agent](https://github.com/samuelbednarcik/elastic-apm-php-agent).