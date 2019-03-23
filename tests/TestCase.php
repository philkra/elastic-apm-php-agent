<?php
/**
 * Created by PhpStorm.
 * User: tepeds
 * Date: 2019-02-09
 * Time: 10:49
 */

namespace PhilKra\Tests;


use PhilKra\Helper\Config;
use PhilKra\Stores\TransactionsStore;
use PHPUnit\Framework\MockObject\MockObject;

abstract class TestCase extends \PHPUnit\Framework\TestCase
{

    protected function assertDurationIsWithinThreshold(int $expectedMilliseconds, float $timedDuration, float $maxOverhead = 10)
    {
        $this->assertGreaterThanOrEqual( $expectedMilliseconds, $timedDuration );

        $overhead = ($timedDuration - $expectedMilliseconds);
        $this->assertLessThanOrEqual( $maxOverhead, $overhead );
    }

    protected function makeConfig(array $overrides = []): Config
    {
        $defaults = [
            'appName' => 'Test Application',
            'apmVersion' => 'v1',
        ];

        return new Config(array_merge($defaults, $overrides));
    }

    protected function makeTransactionsStore(array $transactions = []): TransactionsStore
    {
        /** @var TransactionsStore|MockObject $transactionStore */
        $transactionStore = $this->createMock(TransactionsStore::class);
        $transactionStore
            ->method('jsonSerialize')
            ->willReturn($transactions);
        $transactionStore
            ->method('isEmpty')
            ->willReturn(empty($transactions));

        return $transactionStore;
    }
}