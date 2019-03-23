<?php
/**
 * Created by PhpStorm.
 * User: tepeds
 * Date: 2019-03-20
 * Time: 15:33
 */

namespace PhilKra\Tests\Serializers;

use EnricoStahn\JsonAssert\AssertClass as JsonAssert;
use PhilKra\Serializers\Transactions;
use PhilKra\Tests\TestCase;
use Ramsey\Uuid\Uuid;

class TransactionsTest extends TestCase
{
    private $schemaDirectory = '';
    private $schemaVersionFiles = [];

    public function setUp()
    {
        parent::setUp();

        $this->schemaDirectory = __DIR__ . '/../../schemas';

        $this->schemaVersionFiles['v1'] = $this->schemaDirectory . '/apm-6.5/spec/transactions/v1_transaction.json';
        $this->schemaVersionFiles['v2'] = $this->schemaDirectory . '/apm-6.5/spec/transactions/v2_transaction.json';
    }

    /**
     * @dataProvider apmVersionProvider
     */
    public function testProducesValidJsonData(string $apmVersion)
    {
        $config = $this->makeConfig(['apmVersion' => $apmVersion]);

        /*
         * The TransactionsStore is simply rendered as JSON in the Transactions serializer
         * For now, we can test by providing the data structure expected to represent a
         * Transaction object. The mock TransactionsStore will return that when rendered
         * as JSON.
         */
        $transactions = [
            $this->makeTransactionData($apmVersion)
        ];

        $serializer = new Transactions($config, $this->makeTransactionsStore($transactions));

        $json = json_encode($serializer);

        JsonAssert::assertJsonMatchesSchema(json_decode($json), $this->schemaVersionFiles[$apmVersion]);
    }

    public function apmVersionProvider()
    {
        return [
            'APM Version 1' => ['v1'],
            'APM Version 2' => ['v2'],
        ];
    }

    private function makeTransactionData(string $version = 'v1'): array
    {
        if ($version === 'v2') {
            return [
                'id' => Uuid::uuid4()->toString(),
                'duration' => 1,
                'type' => 'test',
                'trace_id' => Uuid::uuid4()->toString(),
                'span_count' => ['started' => 1, 'dropped' => 0],
            ];
        }

        if ($version === 'v1') {
            return [
                'id' => Uuid::uuid4()->toString(),
                'duration' => 1,
                'type' => 'test',
            ];
        }

        return [];
    }
}
