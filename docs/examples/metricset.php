<?php
//
// Put System Metrics
//
// @link https://www.elastic.co/guide/en/apm/server/current/exported-fields-system.html
// @link https://www.php.net/manual/en/function.sys-getloadavg.php
//
require __DIR__ . '/vendor/autoload.php';

use PhilKra\Agent;

$config = [
    'appName'    => 'examples',
    'appVersion' => '1.0.0-beta',
];

$agent = new Agent($config);
$agent->putMetricset([
    'system.cpu.total.norm.pct' => min(sys_getloadavg()[0]/100, 1),
]);

// more Events to trace ..

$agent->send();
