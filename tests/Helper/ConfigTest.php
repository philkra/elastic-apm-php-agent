<?php

namespace PhilKra\Tests\Helper;

use \PhilKra\Agent;
use PhilKra\Helper\Config;
use PhilKra\Tests\TestCase;

/**
 * Test Case for @see \PhilKra\Helper\Config
 */
final class ConfigTest extends TestCase
{

    /**
     * @covers \PhilKra\Helper\Config::__construct
     * @covers \PhilKra\Agent::getConfig
     * @covers \PhilKra\Helper\Config::getDefaultConfig
     * @covers \PhilKra\Helper\Config::asArray
     */
    public function testControlDefaultConfig()
    {
        $appName = sprintf('app_name_%d', rand(10, 99));
        $agent = new Agent(['appName' => $appName]);

        // Control Default Config
        $config = $agent->getConfig()->asArray();

        $this->assertArrayHasKey('appName', $config);
        $this->assertArrayHasKey('secretToken', $config);
        $this->assertArrayHasKey('serverUrl', $config);
        $this->assertArrayHasKey('hostname', $config);
        $this->assertArrayHasKey('active', $config);
        $this->assertArrayHasKey('timeout', $config);
        $this->assertArrayHasKey('apmVersion', $config);
        $this->assertArrayHasKey('appVersion', $config);
        $this->assertArrayHasKey('env', $config);
        $this->assertArrayHasKey('cookies', $config);
        $this->assertArrayHasKey('httpClient', $config);
        $this->assertArrayHasKey('environment', $config);
        $this->assertArrayHasKey('backtraceLimit', $config);

        $this->assertEquals($config['appName'], $appName);
        $this->assertNull($config['secretToken']);
        $this->assertEquals($config['serverUrl'], 'http://127.0.0.1:8200');
        $this->assertEquals($config['hostname'], gethostname());
        $this->assertTrue($config['active']);
        $this->assertEquals($config['timeout'], 5);
        $this->assertEquals($config['apmVersion'], 'v1');
        $this->assertEquals($config['env'], []);
        $this->assertEquals($config['cookies'], []);
        $this->assertEquals($config['httpClient'], []);
        $this->assertEquals($config['environment'], 'development');
        $this->assertEquals($config['backtraceLimit'], 0);
    }

    /**
     * @depends testControlDefaultConfig
     *
     * @covers  \PhilKra\Helper\Config::__construct
     * @covers  \PhilKra\Agent::getConfig
     * @covers  \PhilKra\Helper\Config::getDefaultConfig
     * @covers  \PhilKra\Helper\Config::asArray
     */
    public function testControlInjectedConfig()
    {
        $init = [
            'appName' => sprintf('app_name_%d', rand(10, 99)),
            'secretToken' => hash('tiger128,3', time()),
            'serverUrl' => sprintf('https://node%d.domain.tld:%d', rand(10, 99), rand(1000, 9999)),
            'appVersion' => sprintf('%d.%d.42', rand(0, 3), rand(0, 10)),
            'frameworkName' => uniqid(),
            'timeout' => rand(10, 20),
            'hostname' => sprintf('host_%d', rand(0, 9)),
            'active' => false,
        ];

        $agent = new Agent($init);

        // Control Default Config
        $config = $agent->getConfig()->asArray();
        foreach ($init as $key => $value) {
            $this->assertEquals($config[$key], $init[$key], 'key: ' . $key);
        }
    }

    /**
     * @depends testControlInjectedConfig
     *
     * @covers  \PhilKra\Helper\Config::__construct
     * @covers  \PhilKra\Agent::getConfig
     * @covers  \PhilKra\Helper\Config::getDefaultConfig
     * @covers  \PhilKra\Helper\Config::get
     */
    public function testGetConfig()
    {
        $init = [
            'appName' => sprintf('app_name_%d', rand(10, 99)),
        ];

        $agent = new Agent($init);
        $this->assertEquals($agent->getConfig()->get('appName'), $init['appName']);
    }

    /**
     * @covers \PhilKra\Helper\Config::__construct
     * @covers \PhilKra\Helper\Config::apmVersion
     */
    public function testCanGetDefaultApmVersion()
    {
        $config = new Config(['appName' => 'Test App']);

        $this->assertEquals(Config::DEFAULT_APM_VERSION, $config->apmVersion());
    }

    /**
     * @covers \PhilKra\Helper\Config::__construct
     * @covers \PhilKra\Helper\Config::apmVersion
     */
    public function testCanUseSpecificApmVersion()
    {
        $config = new Config(['appName' => 'Test App', 'apmVersion' => 'v2']);

        $this->assertEquals('v2', $config->apmVersion());
    }

    /**
     * @covers \PhilKra\Helper\Config::__construct
     * @covers \PhilKra\Helper\Config::useVersion1
     * @covers \PhilKra\Helper\Config::useVersion2
     *
     * @dataProvider apmVersionChecks
     */
    public function testAssertUsingApmVersion(string $version, array $expects)
    {
        $config = new Config(['appName' => 'Test App', 'apmVersion' => $version]);

        $this->assertEquals($expects['v1'], $config->useVersion1());
        $this->assertEquals($expects['v2'], $config->useVersion2());
    }

    public function apmVersionChecks()
    {
        return [
            'APM Version 1' => [
                'v1',
                [
                    'v1' => true,
                    'v2' => false,
                ]
            ],
            'APM Version 2' => [
                'v2',
                [
                    'v1' => false,
                    'v2' => true,
                ]
            ],
        ];
    }
}
