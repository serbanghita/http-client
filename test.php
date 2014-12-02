<?php
error_reporting(E_ALL);

include_once dirname(__FILE__) . '/TransportInterface.php';
include_once dirname(__FILE__) . '/Transport.php';

$requestProduct = new \stdClass();
$requestProduct->method = 'getProduct';
$requestProduct->params = array('id' => 1234);
$requestProduct->id = rand(0,10);

$requestOrder = new \stdClass();
$requestOrder->method = 'getOrder';
$requestOrder->params = array('id' => 5678);
$requestOrder->id = rand(0,10);

$client = new \GenericApiClient\Transport\Socks();
$client->connect('http-client.serbang', 80);
$client->send('GET', '/server.php?page=getProduct', json_encode($requestProduct));
$client->send('GET', '/server.php?page=getOrder', json_encode($requestOrder));
$client->close();
exit('Good!');