# Installation
The recommended way to install the agent is through [Composer](http://getcomposer.org).
Please note that this agent supports from now on *only* v2 of the APM intake API, therefore please specify the verioning in your composer.json, to `2.*`.

Run the following composer command:

```bash
php composer.phar require philkra/elastic-apm-php-agent:2.*
```

After installing, you need to require Composer's autoloader:

```php
require 'vendor/autoload.php';
```