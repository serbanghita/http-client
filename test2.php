<?php
namespace GenericApiClient\Transport;

use GenericApiClient\Transport\Exception;

\error_reporting(E_ALL);

include_once dirname(__FILE__) . '/Message/MessageInterface.php';
include_once dirname(__FILE__) . '/Message/AbstractMessage.php';
include_once dirname(__FILE__) . '/Message/Request.php';

include_once dirname(__FILE__) . '/Transport/TransportInterface.php';
include_once dirname(__FILE__) . '/Transport/AbstractTransport.php';
include_once dirname(__FILE__) . '/Transport/Socks.php';
include_once dirname(__FILE__) . '/Transport/Exception/Exception.php';
include_once dirname(__FILE__) . '/Transport/Exception/RuntimeException.php';

function generateRandomString($length = 10) {
    $characters = '0123456789abcdefghijklmnopqrstuvwxyzABCDEFGHIJKLMNOPQRSTUVWXYZ';
    $charactersLength = strlen($characters);
    $randomString = '';
    for ($i = 0; $i < $length; $i++) {
        $randomString .= $characters[rand(0, $charactersLength - 1)];
    }
    return $randomString;
}



$transport = new Socks();
$transport->setHost('http-client.serbang'); // Replace with your host.
try {
    $transport->connect();
    $transport->request()->setPath('/server.php?page=' . generateRandomString(5));
    $transport->request()->setBody(generateRandomString(10));
    //$transport->request()->addHeader('Connection', 'keep-alive');
    $transport->request()->addHeader('Content-type', 'application/json');
    $transport->send();
    $transport->read();
    $transport->close();
} catch(Exception\Exception $e) {
    var_dump($e->getCode());
    var_dump($e->getMessage());
} catch (Exception\RuntimeException $e) {
    var_dump($e->getCode());
    var_dump($e->getMessage());
}