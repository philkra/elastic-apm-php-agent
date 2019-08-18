<?php
//
// This example demonstrates the use of parent transactions to link transactions together.
//
require __DIR__ . '/vendor/autoload.php';

use PhilKra\Agent;

$config = [
    'appName'    => 'examples',
    'appVersion' => '1.0.0-beta',
    'env'        => ['REMOTE_ADDR'],
];

$agent = new Agent($config);

// Start a new parent Transaction
$parent = $agent->startTransaction('GET /users');

// Start a child Transaction and set the Parent
$childOne = $agent->startTransaction('http.session.get.auth.data');
$childOne->setParent($parent);

// Do stuff ..
usleep(rand(1000, 99999));

$agent->stopTransaction($childOne->getTransactionName());

// Start another child Transaction and set the Parent
$childTwo = $agent->startTransaction('elasticsearch.search.active.users');
$childTwo->setParent($parent);

// Do stuff ..
usleep(rand(1000, 99999));

$agent->stopTransaction($childTwo->getTransactionName());

// Create a 3rd child Transaction that is throwing an exception
$childThree = $agent->startTransaction('division.by.zero');
$childThree->setParent($parent);
$agent->captureThrowable( new Exception("division by 0 exception"), [], $childThree );
$agent->stopTransaction($childThree->getTransactionName());

$agent->stopTransaction($parent->getTransactionName());

var_dump($agent->send());
