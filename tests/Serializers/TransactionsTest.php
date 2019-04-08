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

class TransactionsTest extends TestCase
{
    private $schemaDirectory = '';
    private $schemaVersionFiles = [];

    public function setUp()
    {
        parent::setUp();

        $this->schemaDirectory = __DIR__ . '/../../schemas';

        $this->schemaVersionFiles['v1'] = $this->schemaDirectory . '/apm-6.5/docs/spec/transactions/v1_transaction.json';
        $this->schemaVersionFiles['v2'] = $this->schemaDirectory . '/apm-6.5/docs/spec/transactions/v2_transaction.json';
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

        $this->assertValidJson($apmVersion, $json);
    }

    private function assertValidJson(string $apmVersion, string $json)
    {
        switch ($apmVersion) {
            case 'v1':
                $object = json_decode($json);
                break;

            case 'v2':
                $object = json_decode($json)[0];
                break;
        }

        JsonAssert::assertJsonMatchesSchema($object, $this->schemaVersionFiles[$apmVersion]);
    }
}
