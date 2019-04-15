# Elastic APM: PHP Agent

[![Build Status](https://travis-ci.com/philkra/elastic-apm-php-agent.svg?branch=master)](https://travis-ci.org/philkra/elastic-apm-php-agent)

This is a PHP agent for Elastic.co's APM product: https://www.elastic.co/solutions/apm. Laravel & Lumen package https://github.com/philkra/elastic-apm-laravel

*Please note* that currently only the `v1` intake API of the APM server is supported, `v2` is in planing.

## Installation
The recommended way to install the agent is through [Composer](http://getcomposer.org).

Run the following composer command

```bash
php composer.phar require philkra/elastic-apm-php-agent
```

After installing, you need to require Composer's autoloader:

```php
require 'vendor/autoload.php';
```

## Usage

### Initialize the Agent with minimal Config
```php
$agent = new \PhilKra\Agent( [ 'appName' => 'demo' ] );
```
When creating the agent, you can directly inject shared contexts such as user, tags and custom.
```php
$agent = new \PhilKra\Agent( [ 'appName' => 'with-custom-context' ], [
  'user' => [
    'id'    => 12345,
    'email' => 'email@acme.com',
  ],
  'tags' => [
    // ... more key-values
  ],
  'custom' => [
    // ... more key-values
  ]
] );
```

### Capture Errors and Exceptions
The agent can capture all types or errors and exceptions that are implemented from the interface `Throwable` (http://php.net/manual/en/class.throwable.php).
```php
$agent->captureThrowable( new Exception() );
```

### Adding spans
Addings spans (https://www.elastic.co/guide/en/apm/server/current/transactions.html#transaction-spans) is easy.
Please consult the documentation for your exact needs. Below is an example for adding a MySQL span.

```php
$trxName = 'GET /some/transaction/name';

// create the agent
$agent = new \PhilKra\Agent(['appName' => 'Demo with Spans']);

// start a new transaction
$transaction = $agent->startTransaction($trxName);

// create a span
$spans = [];
$spans[] = [
  'name' => 'Your Span Name. eg: ORM Query',
  'type' => 'db.mysql.query',
  'start' => 300, // when did tht query start, relative to the transaction start, in milliseconds
  'duration' => 23, // duration, in milliseconds
  'stacktrace' => [
    [
      'function' => "\\YourOrMe\\Library\\Class::methodCall()",
      'abs_path' => '/full/path/to/file.php',
      'filename' => 'file.php',
      'lineno' => 30,
      'library_frame' => false, // indicated whether this code is 'owned' by an (external) library or not
      'vars' => [
        'arg1' => 'value',
        'arg2' => 'value2',
      ],
      'pre_context' => [ // lines of code leading to the context line
        '<?php',
        '',
        '// executing query below',
      ],
      'context_line' => '$result = mysql_query("select * from non_existing_table")', // source code of context line
      'post_context' => [// lines of code after to the context line
        '',
        '$table = $fakeTableBuilder->buildWithResult($result);',
        'return $table;',
      ],
    ],
  ],
  'context' => [
    'db' => [
      'instance' => 'my_database', // the database name
      'statement' => 'select * from non_existing_table', // the query being executed
      'type' => 'sql',
      'user' => 'root', // the user executing the query (don't use root!)
    ],
  ],
];

// add the array of spans to the transaction
$transaction->setSpans($spans);

// Do some stuff you want to watch ...
sleep(1);

$agent->stopTransaction($trxName);

// send our transactions to te apm
$agent->send();
```

### Transaction without minimal Meta data and Context
```php
$trxName = 'Demo Simple Transaction';
$agent->startTransaction( $trxName );
// Do some stuff you want to watch ...
$agent->stopTransaction( $trxName );
```

### Transaction with Meta data and Contexts
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

### Example of a Transaction
This example illustrates how you can monitor a call to another web service.
```php
$agent = new \PhilKra\Agent( [ 'appName' => 'example' ] );

$endpoint = 'https://acme.com/api/';
$payload  = [ 'foo' => 'bar' ];
$trxName  = sprintf('POST %s', $endpoint);
$client   = new GuzzleHttp\Client();

// Start the Transaction
$agent->startTransaction( $trxName );

// Do the call via curl/Guzzle e.g.
$response = $client->request('POST', $endpoint, [
    'json' => $payload
]);

// Stop the Transaction tracing, attach the Status and the sent Payload
$agent->stopTransaction( $trxName, [
    'status'  => $response->getStatusCode(),
    'payload' => $payload,
] );

// Send the collected Traces to the APM server
$agent->send();
```

### Configuration
```
appName       : Name of this application, Required
appVersion    : Application version, Default: ''
serverUrl     : APM Server Endpoint, Default: 'http://127.0.0.1:8200'
secretToken   : Secret token for APM Server, Default: null
hostname      : Hostname to transmit to the APM Server, Default: gethostname()
active        : Activate the APM Agent, Default: true
timeout       : Guzzle Client timeout, Default: 5
apmVersion    : APM Server Intake API version, Default: 'v1'
env           : $_SERVER vars to send to the APM Server, empty set sends all. Keys are case sensitive, Default: []
cookies       : Cookies to send to the APM Server, empty set sends all. Keys are case sensitive, Default: []
httpClient    : Extended GuzzleHttp\Client Default: []
backtraceLimit: Depth of a transaction backtrace, Default: unlimited
```

Detailed `GuzzleHttp\Client` options can be found [here](http://docs.guzzlephp.org/en/stable/request-options.html#request-options).

#### Example of an extended Configuration
```php
$config = [
    'appName'     => 'My WebApp',
    'appVersion'  => '1.0.42',
    'serverUrl'   => 'http://apm-server.example.com',
    'secretToken' => 'DKKbdsupZWEEzYd4LX34TyHF36vDKRJP',
    'hostname'    => 'node-24.app.network.com',
    'env'         => ['DOCUMENT_ROOT', 'REMOTE_ADDR'],
    'cookies'     => ['my-cookie'],
    'httpClient'  => [
        'verify' => false,
        'proxy'  => 'tcp://localhost:8125'
    ],
];
$agent = new \PhilKra\Agent($config);
```

## Tests
```bash
vendor/bin/phpunit
```

## Knowledgebase

### Disable Agent for CLI
In case you want to disable the agent dynamically for hybrid SAPI usage, please use the following snippet.
```php
'active' => PHP_SAPI !== 'cli'
```
In case for the Laravel APM provider:
```php
'active' => PHP_SAPI !== 'cli' && env('ELASTIC_APM_ACTIVE', false)
```
Thank you to @jblotus, (https://github.com/philkra/elastic-apm-laravel/issues/19)
