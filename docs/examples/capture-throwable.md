# Capture Errors and Exceptions
The agent can capture all types or errors and exceptions that are implemented from the interface [`Throwable`](http://php.net/manual/en/class.throwable.php). When capturing an _error_, you can a context and highly recommended a parent `transaction` as illustrated in the following snippet.

By doing so you increase the tracability of the error.

```php
// Setup Agent
$config = [
    'appName'    => 'examples',
    'appVersion' => '1.0.0-beta',
];
$agent = new Agent($config);

// start a new transaction or use an existing one
$transaction = $agent->startTransaction('Failing-Transaction');

try {
    //
    // do stuff that generates an Exception
    //
}
catch(Exception $e) {
    $agent->captureThrowable($e, [], $transaction);
    // handle Exception ..
}

// do some more stuff ..
$agent->stopTransaction($transaction->getTransactionName());
```
