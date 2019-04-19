# Agent Configuration

For the sake of semantics, the configuration array is now a nested array and that features value addressing with the dot notation.

## Default Configuration
This array illustrated the default configuration.
```php
[
    'transport'      => [
        'method' => 'http',
        'host'   => 'http://127.0.0.1:8200',
        'config' => [
            'timeout' => 5,
        ],
    ],
    'secretToken'    => null,
    'hostname'       => gethostname(),
    'appVersion'     => '0.0.0',
    'active'         => true,
    'environment'    => 'development',
    'env'            => [],
    'cookies'        => [],
    'backtraceLimit' => 0,
];
```

## Transport
The agent's socket support was introduced with `v6.7.0`. The usage of the `Socket` transport class is faster and provides a lower memory footprint than the `Http` transport class. In other words consider the usage of sockets of the previously conventional http.

### Socket


### Http
The default timeout is 5 seconds.
