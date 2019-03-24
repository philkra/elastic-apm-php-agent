<?php
/**
 * Created by PhpStorm.
 * User: tepeds
 * Date: 2019-03-24
 * Time: 09:22
 */

namespace PhilKra\Tests\Middleware;

use GuzzleHttp\Psr7\Response;
use PhilKra\Agent;
use PhilKra\Events\Error;
use PhilKra\Middleware\Connector;
use PhilKra\Stores\ErrorsStore;
use PhilKra\Tests\TestCase;

class ConnectorTest extends TestCase
{
    /**
     * @dataProvider apmVersionProvider
     */
    public function testSetsContentTypeHeader(string $apmVersion)
    {
        $transactionStore = $this->makeTransactionsStore([$this->makeTransactionData($apmVersion)]);

        $connector = $this->makeConnector(['apmVersion' => $apmVersion], [new Response()]);

        $connector->sendTransactions($transactionStore);

        $request = $this->getRequest();
        $this->assertEquals('application/json', $request->getHeader('Content-Type')[0]);
    }

    /**
     * @dataProvider apmVersionProvider
     */
    public function testSetsUserAgentHeader(string $apmVersion)
    {
        $transactionStore = $this->makeTransactionsStore([$this->makeTransactionData($apmVersion)]);

        $connector = $this->makeConnector(['apmVersion' => $apmVersion], [new Response()]);

        $connector->sendTransactions($transactionStore);

        $request = $this->getRequest();
        $this->assertEquals(sprintf('elasticapm-php/%s', Agent::VERSION), $request->getHeader('User-Agent')[0]);
    }

    /**
     * @dataProvider apmVersionProvider
     */
    public function testDoesNotSetAuthorizationHeaderWithoutToken(string $apmVersion)
    {
        $transactionStore = $this->makeTransactionsStore([$this->makeTransactionData($apmVersion)]);

        $connector = $this->makeConnector(['apmVersion' => $apmVersion], [new Response()]);

        $connector->sendTransactions($transactionStore);

        $request = $this->getRequest();
        $this->assertFalse($request->hasHeader('Authorization'));
    }

    /**
     * @dataProvider apmVersionProvider
     */
    public function testSetsAuthorizationHeaderWithToken(string $apmVersion)
    {
        $transactionStore = $this->makeTransactionsStore([$this->makeTransactionData($apmVersion)]);

        $connector = $this->makeConnector(
            ['apmVersion' => $apmVersion, 'secretToken' => 'abc123'],
            [new Response()]
        );

        $connector->sendTransactions($transactionStore);

        $request = $this->getRequest();
        $this->assertTrue($request->hasHeader('Authorization'));
        $this->assertEquals(sprintf('Bearer %s', 'abc123'), $request->getHeader('Authorization')[0]);
    }

    /**
     * @dataProvider apmVersionProvider
     */
    public function testSendsErrorsWithPostRequest(string $apmVersion)
    {
        $errors = new ErrorsStore();
        $errors->register(new Error(new \Exception('An error occurred'), ['cookies' => []]));

        $connector = $this->makeConnector(['apmVersion' => $apmVersion], [new Response()]);

        $connector->sendErrors($errors);

        $this->assertSendError($apmVersion);
    }

    /**
     * @dataProvider apmVersionProvider
     */
    public function testSendsSingleTransactionWithPostRequest(string $apmVersion)
    {
        $transactionStore = $this->makeTransactionsStore([$this->makeTransactionData($apmVersion)]);

        $connector = $this->makeConnector(['apmVersion' => $apmVersion], [new Response()]);

        $connector->sendTransactions($transactionStore);

        $this->assertSendSingleTransaction($apmVersion);
    }

    /**
     * @dataProvider apmVersionProvider
     */
    public function testSendsMultipleTransactionsWithPostRequest(string $apmVersion)
    {
        $transactions = [
            $this->makeTransactionData($apmVersion),
            $this->makeTransactionData($apmVersion),
            $this->makeTransactionData($apmVersion),
        ];

        $transactionStore = $this->makeTransactionsStore($transactions);

        $connector = $this->makeConnector(
            ['apmVersion' => $apmVersion],
            $this->makeSendTransactionResponses($apmVersion, count($transactions))
        );

        $connector->sendTransactions($transactionStore);

        $this->assertSendMultipleTransactions($apmVersion, count($transactions));
    }

    private function makeConnector(array $testConfig, array $httpResponses): Connector
    {
        $config = $this->makeConfig(array_merge(['serverUrl' => 'https://example.com'], $testConfig));
        $client = $this->makeHttpClient($httpResponses);

        return new Connector($config, $client);
    }

    private function assertSendError(string $apmVersion)
    {
        $this->assertEquals(1, $this->requestCount());

        $request = $this->getRequest();
        $this->assertEquals('POST', $request->getMethod());
        $this->assertEquals('https://example.com/' . $apmVersion . '/errors', $request->getUri());
        $body = $request->getBody()->getContents();
        $this->assertContains('An error occurred', $body);
    }

    private function assertSendSingleTransaction(string $apmVersion)
    {
        $this->assertEquals(1, $this->requestCount());

        $request = $this->getRequest();
        $this->assertEquals('POST', $request->getMethod());
        $this->assertEquals('https://example.com/' . $apmVersion . '/transactions', $request->getUri());
    }

    private function makeSendTransactionResponses(string $apmVersion, int $count): array
    {
        $responses = [];

        for ($i = 0; $i < $count; $i++) {
            $responses[] = new Response();
        }

        return $responses;
    }

    private function assertSendMultipleTransactions(string $apmVersion, int $count)
    {
        switch ($apmVersion) {
            case 'v1':
                $this->assertSendMultipleTransactionsV1($count);
                break;

            case 'v2':
                $this->assertSendMultipleTransactionsV2($count);
                break;

        }
    }

    private function assertSendMultipleTransactionsV1(int $count)
    {
        // Makes a single POST for multiple transactions
        $this->assertEquals(1, $this->requestCount());

        $request = $this->getRequest();
        $this->assertEquals('POST', $request->getMethod());
        $this->assertEquals('https://example.com/v1/transactions', $request->getUri());

        $body = json_decode($request->getBody()->getContents(), true);

        // Consistent with v1 schema
        $this->assertCount($count, $body['transactions']);
    }

    private function assertSendMultipleTransactionsV2(int $count)
    {
        // Makes a POST for each transaction
        $this->assertEquals($count, $this->requestCount());

        $request = $this->getRequest();
        $this->assertEquals('POST', $request->getMethod());
        $this->assertEquals('https://example.com/v2/transactions', $request->getUri());

        $body = json_decode($request->getBody()->getContents(), true);

        // Consistent with v2 schema
        $this->assertFalse(array_key_exists('transactions', $body));
    }
}
