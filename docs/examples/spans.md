# Adding spans
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

// send our transactions to the apm
$agent->send();
```
