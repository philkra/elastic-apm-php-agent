<?php
/**
 * Created by PhpStorm.
 * User: tepeds
 * Date: 2019-02-09
 * Time: 10:49
 */

namespace PhilKra\Tests;


use GuzzleHttp\Client;
use GuzzleHttp\Handler\MockHandler;
use GuzzleHttp\HandlerStack;
use GuzzleHttp\Middleware;
use GuzzleHttp\Psr7\Request;
use PhilKra\Helper\Config;
use PhilKra\Stores\TransactionsStore;
use PHPUnit\Framework\MockObject\MockObject;
use Ramsey\Uuid\Uuid;

abstract class TestCase extends \PHPUnit\Framework\TestCase
{
    /** @var Request[] */
    protected $container = [];

    public function apmVersionProvider()
    {
        return [
            'APM Version 1' => ['v1'],
            'APM Version 2' => ['v2'],
        ];
    }

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

    protected function makeTransactionData(string $version = 'v1'): array
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

    protected function makeHttpClient(array $responses = []): Client
    {
        $mock = new MockHandler($responses);

        $this->container = [];
        $history = Middleware::history($this->container);

        $handler = HandlerStack::create($mock);
        $handler->push($history);

        return new Client(['handler' => $handler]);
    }

    protected function requestCount(): int
    {
        return count($this->container);
    }

    protected function getRequest(int $index = 0): Request
    {
        if (empty($this->container)) {
            $this->fail('HTTP transaction container is empty');
        }

        if (empty($this->container[$index])) {
            $this->fail('HTTP transaction container does not have index ' . $index);
        }

        return $this->container[$index]['request'];
    }
}