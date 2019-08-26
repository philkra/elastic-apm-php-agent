
# Knowledgebase

# Disable Agent for CLI
In case you want to disable the agent dynamically for hybrid SAPI usage, please use the following snippet.
```php
'active' => PHP_SAPI !== 'cli'
```
In case for the Laravel APM provider:
```php
'active' => PHP_SAPI !== 'cli' && env('ELASTIC_APM_ACTIVE', false)
```
Thank you to @jblotus, (https://github.com/philkra/elastic-apm-laravel/issues/19)