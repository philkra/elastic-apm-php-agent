# Elastic APM: PHP Agent

[![Build Status](https://travis-ci.org/philkra/elastic-apm-php-agent.svg?branch=master)](https://travis-ci.org/philkra/elastic-apm-php-agent)

This is a PHP agent for Elastic.co's APM product: https://www.elastic.co/solutions/apm. Please note that this agent is still **experimental** and not ready for any production usage.

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

### Capture Errors and Exceptions
The agent can capture all types or errors and exceptions that are implemented from the interface `Throwable` (http://php.net/manual/en/class.throwable.php).
```php
$agent->captureThrowable( new Exception() );
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
