# Elastic APM: PHP Agent

[![Build Status](https://travis-ci.org/philkra/elastic-apm-agent-php.svg?branch=master)](https://travis-ci.org/philkra/elastic-apm-agent-php)

This is a PHP agent for Elastic.co's APM product: https://www.elastic.co/solutions/apm

## Installation
The recommended way to install the agent is through [Composer](http://getcomposer.org).

Run the following composer command

```bash
php composer.phar require philkra/elastic-apm-agent-php
```

After installing, you need to require Composer's autoloader:

```php
require 'vendor/autoload.php';
```

## Tests
```bash
vendor/bin/phpunit
```
