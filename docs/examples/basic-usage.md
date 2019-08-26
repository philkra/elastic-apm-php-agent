# Transaction without minimal Meta data and Context
```php
$transaction = $agent->startTransaction('Simple Transaction');
// Do some stuff you want to watch ...
$agent->stopTransaction($transaction->getTransactionName());
```

# Transaction with Meta data and Contexts
```php
$trxName = 'Demo Transaction with more Data';
$agent->startTransaction( $trxName );
// Do some stuff you want to watch ...
$agent->stopTransaction( $trxName, [
    'result' => '200',
    'type'   => 'demo'
] );
$agent->getTransaction( $trxName )->setUserContext( [
    'id'    => 12345,
    'email' => "hello@acme.com",
 ] );
 $agent->getTransaction( $trxName )->setCustomContext( [
    'foo' => 'bar',
    'bar' => [ 'foo1' => 'bar1', 'foo2' => 'bar2' ]
] );
$agent->getTransaction( $trxName )->setTags( [ 'k1' => 'v1', 'k2' => 'v2' ] );
```

# Example of a Transaction
This example illustrates how you can monitor a call to another web service.
```php
$agent = new \PhilKra\Agent([ 'appName' => 'examples' ]);

$endpoint = 'https://acme.com/api/';
$payload  = [ 'foo' => 'bar' ];
$client   = new GuzzleHttp\Client();

// Start the Transaction
$transaction = $agent->startTransaction('POST https://acme.com/api/');

// Do the call via curl/Guzzle e.g.
$response = $client->request('POST', $endpoint, [
    'json' => $payload
]);

// Stop the Transaction tracing, attach the Status and the sent Payload
$agent->stopTransaction($transaction->getTransactionName(), [
    'status'  => $response->getStatusCode(),
    'payload' => $payload,
]);

// Send the collected Traces to the APM server
$agent->send();
```
