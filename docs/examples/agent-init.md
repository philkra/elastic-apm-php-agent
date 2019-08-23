# Initialize the Agent

See all configuration options [here](https://github.com/philkra/elastic-apm-php-agent/blob/master/docs/config.md).

## With minimal Config
```php
$agent = new \PhilKra\Agent( [ 'appName' => 'demo' ] );
```

## With elaborate Config
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