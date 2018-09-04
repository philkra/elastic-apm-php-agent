# Elastic APM: PHP Agent

[![Build Status](https://travis-ci.org/philkra/elastic-apm-php-agent.svg?branch=master)](https://travis-ci.org/philkra/elastic-apm-php-agent)

This is a PHP agent for Elastic.co's APM product: https://www.elastic.co/solutions/apm.

*New:* Laravel & Lumen package https://github.com/philkra/elastic-apm-laravel

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
// create the agent
$agent = new \PhilKra\Agent(['appName' => 'Demo with Spans']);

// start a new transaction
$transaction = $agent->startTransaction('GET /some/transaction/name');

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

## Tests
```bash
vendor/bin/phpunit
```
