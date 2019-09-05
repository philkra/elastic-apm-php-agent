# Adding spans
Please consult the documentation for your exact needs.
Below is an example to add spans for MySQL, Redis and generic request wraped by a parent span.

![Dashboard](https://github.com/philkra/elastic-apm-php-agent/blob/master/docs/examples/blob/span_dashboard.png "Spans Dashboard") ![Stacktrace](https://github.com/philkra/elastic-apm-php-agent/blob/master/docs/examples/blob/span_stacktrace.png "Span Stacktrace")

```php
// create the agent
$agent = new \PhilKra\Agent(['appName' => 'examples']);

$agent = new Agent($config);

// Span
// start a new transaction
$parent = $agent->startTransaction('POST /auth/examples/spans');

// burn some time
usleep(rand(10, 25));

// Create Span
$spanParent = $agent->factory()->newSpan('Authenication Workflow', $parent);
$spanParent->start();

// Lookup the User 'foobar' in the database
$spanDb = $agent->factory()->newSpan('DB User Lookup', $spanParent);
$spanDb->setType('db.mysql.query');
$spanDb->setAction('query');
$spanDb->start();

// do some db.mysql.query action ..
usleep(rand(100, 300));

$spanDb->stop();
// Attach addition/optional Context
$spanDb->setContext(['db' => [
    'instance'  => 'my_database', // the database name
    'statement' => 'select * from non_existing_table where username = "foobar"', // the query being executed
    'type'      => 'sql',
    'user'      => 'user',
]]);
$agent->putEvent($spanDb);

// Stach the record into Redis
$spanCache = $agent->factory()->newSpan('DB User Lookup', $spanParent);
$spanCache->setType('db.redis.query');
$spanCache->setAction('query');
$spanCache->start();


// do some db.mysql.query action ..
usleep(rand(10, 30));

$spanCache->stop();
$spanCache->setContext(['db' => [
    'instance'  => 'redis01.example.foo',
    'statement' => 'SET user_foobar "12345"',
]]);
$agent->putEvent($spanCache);

// Create another Span that is a parent span
$spanHash = $agent->factory()->newSpan('Validate Credentials', $spanParent);
$spanHash->start();

// do some password hashing and matching ..
usleep(rand(50, 100));

$spanHash->stop();
$agent->putEvent($spanHash);

// Create another Span that is a parent span
$spanSt = $agent->factory()->newSpan('Span with stacktrace', $spanParent);
$spanSt->start();

// burn some fictive time ..
usleep(rand(250, 350));
$spanSt->setStackTrace([
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
    ]
]);

$spanSt->stop();
$agent->putEvent($spanSt);

$spanParent->stop();

// Do some stuff you want to watch ...
usleep(rand(100, 250));

$agent->putEvent($spanParent);

$agent->stopTransaction($parent->getTransactionName());

// send our transactions to the apm
$agent->send();
```
