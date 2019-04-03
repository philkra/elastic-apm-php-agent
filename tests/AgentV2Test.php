<?php
namespace PhilKra\Tests;

use \PhilKra\AgentV2;


/**
 * Test Case for @see \PhilKra\Agent
 */
final class AgentV2Test extends TestCase {

  /**
   * @covers \PhilKra\Agent::__construct
   * @covers \PhilKra\Agent::startTransaction
   * @covers \PhilKra\Agent::stopTransaction
   * @covers \PhilKra\Agent::getTransaction
   */
  public function testTransactionSummaryCountsEmptyListOfSpans() {
    $agent = new AgentV2( [ 'appName' => 'phpunit_1' ] );

    // Create a Transaction, wait and Stop it
    $name = 'trx';
    $agent->startTransaction( $name );
    usleep( 10 * 1000 ); // sleep milliseconds
    $agent->stopTransaction( $name );

    // Transaction Summary must be populated
    $summary = $agent->getTransaction( $name )->getSummary();

    $this->assertArrayHasKey( 'started_spans', $summary );
    $this->assertArrayHasKey( 'dropped_spans', $summary );

    $this->assertEquals(0, $summary['started_spans'] );
    $this->assertEquals(0, $summary['dropped_spans'] );

  }

  public function testTransactionSummaryCountsSpans() {
    $agent = new AgentV2( [ 'appName' => 'phpunit_1' ] );

    // Create a Transaction with two span
    $name = 'trx';
    $transaction = $agent->startTransaction( $name );
    $spans = [];
    $spans[] = $this->getExampleSpan();
    $spans[] = $this->getExampleSpan();
    $transaction->setSpans($spans);
    usleep( 10 * 1000 ); // sleep milliseconds
    $agent->stopTransaction( $name );

    // Transaction Summary must be populated
    $summary = $agent->getTransaction( $name )->getSummary();

    $this->assertArrayHasKey( 'started_spans', $summary );
    $this->assertArrayHasKey( 'dropped_spans', $summary );

    $this->assertEquals(2, $summary['started_spans'] );
    $this->assertEquals(0, $summary['dropped_spans'] );

  }

  public function testAgentSend() {
    $agent = new AgentV2( [ 'appName' => 'phpunit_1' ] );

    // Create a Transaction with two span
    $name = 'trx';
    $transaction = $agent->startTransaction( $name );
    $spans = [];
    $spans[] = $this->getExampleSpan();
    $spans[] = $this->getExampleSpan();
    $transaction->setSpans($spans);
    usleep( 10 * 1000 ); // sleep milliseconds
    $agent->stopTransaction( $name );

    $status = $agent->send();
    $this->assertTrue($status);
  }
  /**
   * @return array
   */
  protected function getExampleSpan()
  {
    return [
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
  }

}
