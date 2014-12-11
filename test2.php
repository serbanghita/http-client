<?php
error_reporting(E_ALL);

include_once dirname(__FILE__) . '/TransportInterface.php';
include_once dirname(__FILE__) . '/Transport.php';
include_once dirname(__FILE__) . '/Options.php';
include_once dirname(__FILE__) . '/Headers/Headers.php';
include_once dirname(__FILE__) . '/Request/Options.php';
include_once dirname(__FILE__) . '/Request/Request.php';

function generateRandomString($length = 10) {
    $characters = '0123456789abcdefghijklmnopqrstuvwxyzABCDEFGHIJKLMNOPQRSTUVWXYZ';
    $charactersLength = strlen($characters);
    $randomString = '';
    for ($i = 0; $i < $length; $i++) {
        $randomString .= $characters[rand(0, $charactersLength - 1)];
    }
    return $randomString;
}

$client = new \GenericApiClient\Transport\Socks();
    $transportOptions = new \GenericApiClient\Transport\Options();
    $transportOptions->host = 'http-client.serbang';
    $transportOptions->port = 80;
$client->connect($transportOptions);

    $requestOptions = new \GenericApiClient\Transport\Request\Options();
    $requestOptions->path = '/server.php?page=' . generateRandomString(5);
    $requestOptions->body = generateRandomString(10);
    $requestHeaders = new \GenericApiClient\Transport\Headers\Headers(array(
        'Host' => $transportOptions->host,
        'Connection' => 'keep-alive',
        'Content-length' => strlen($requestOptions->body),
        'Content-type' => 'application/json',
        'Accept' => '*/*'
    ));
        $request = new \GenericApiClient\Transport\Request\Request($requestHeaders, $requestOptions);
$client->send($request);
$client->close();