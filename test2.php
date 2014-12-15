<?php
error_reporting(E_ALL);

include_once dirname(__FILE__) . '/TransportInterface.php';
include_once dirname(__FILE__) . '/Transport.php';
include_once dirname(__FILE__) . '/Request.php';
include_once dirname(__FILE__) . '/AbstractMessage.php';
include_once dirname(__FILE__) . '/MessageInterface.php';

function generateRandomString($length = 10) {
    $characters = '0123456789abcdefghijklmnopqrstuvwxyzABCDEFGHIJKLMNOPQRSTUVWXYZ';
    $charactersLength = strlen($characters);
    $randomString = '';
    for ($i = 0; $i < $length; $i++) {
        $randomString .= $characters[rand(0, $charactersLength - 1)];
    }
    return $randomString;
}

$transport = new \GenericApiClient\Transport\Socks();
$transport->addOption('host', 'http-client.serbang');
$transport->addOption('port', 80);
$transport->connect();

$transport->request()->addOption('path', '/server.php?page=' . generateRandomString(5));
$transport->request()->addOption('body', generateRandomString(10));
$transport->request()->addHeader('Connection', 'keep-alive');
$transport->request()->addHeader('Content-type', 'application/json');

$transport->send();
$transport->close();