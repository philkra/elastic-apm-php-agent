<?php
/**
 * Created by PhpStorm.
 * User: tepeds
 * Date: 2019-03-20
 * Time: 15:33
 */

namespace PhilKra\Tests\Serializers;

use JsonSchema\Validator;
use PhilKra\Events\Transaction;
use PhilKra\Helper\Config;
use PhilKra\Serializers\Transactions;
use PhilKra\Stores\TransactionsStore;
use PHPUnit\Framework\MockObject\MockObject;
use PHPUnit\Framework\TestCase;
use Ramsey\Uuid\Uuid;

class TransactionsTest extends TestCase
{
    public function testProducesExceptedDefault()
    {
        $config = new Config(['appName' => 'Test Application']);

        /** @var TransactionsStore|MockObject $transactionStore */
        $transactionStore = $this->createMock(TransactionsStore::class);
        $transactionStore->expects($this->once())
            ->method('jsonSerialize')
            ->willReturn([
                [
                    'id' => Uuid::uuid4()->toString(),
                    'duration' => 1,
                    'type' => 'test',
                ]
            ]);

        $serializer = new Transactions($config, $transactionStore);

        $data = json_decode(json_encode($serializer));

        $validator = new Validator;
        $validator->validate($data, (object)['$ref' => 'file://' . realpath('schemas/apm-6.5/spec/transactions/v1_transaction.json')]);

        if (!$validator->isValid()) {
            echo "JSON does not validate. Violations:\n";
            foreach ($validator->getErrors() as $error) {
                echo sprintf("[%s] %s\n", $error['property'], $error['message']);
            }
            $this->fail('JSON does not validate');
        }
    }
}
