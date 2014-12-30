<?php
namespace HttpClient\Transport;

use HttpClient\Transport\Exception;

\error_reporting(E_ALL);

include_once dirname(__FILE__) . '/../../lib/Message/MessageInterface.php';
include_once dirname(__FILE__) . '/../../lib/Message/AbstractMessage.php';
include_once dirname(__FILE__) . '/../../lib/Message/Request.php';

include_once dirname(__FILE__) . '/../../lib/Transport/TransportInterface.php';
include_once dirname(__FILE__) . '/../../lib/Transport/AbstractTransport.php';
include_once dirname(__FILE__) . '/../../lib/Transport/Socks.php';
include_once dirname(__FILE__) . '/../../lib/Transport/Exception/Exception.php';
include_once dirname(__FILE__) . '/../../lib/Transport/Exception/RuntimeException.php';

function generateRandomString($length = 10)
{
    $characters = '0123456789abcdefghijklmnopqrstuvwxyzABCDEFGHIJKLMNOPQRSTUVWXYZ';
    $charactersLength = strlen($characters);
    $randomString = '';
    for ($i = 0; $i < $length; $i++) {
        $randomString .= $characters[rand(0, $charactersLength - 1)];
    }
    return $randomString;
}



$transport = new Socks();
// http://demo.mobiledetect.net/test/jsonrpc.php
// http://http-client.serbang/server.php
$transport->setHost('http-client.serbang');
//$transport->setProxy('proxy.avangate.local:8080');
try {
    $transport->connect();
    $transport->request()->setPath('/tests/providers/response/jsonrpc.php?page=' . generateRandomString(5));
    $transport->request()->setBody(generateRandomString(10));
    //$transport->request()->addHeader('Connection', 'keep-alive');
    $transport->request()->addHeader('Content-type', 'application/json');
    $transport->send();
    $responseBody = $transport->read();
        echo "\n" . '---Begin Response Body---' . "\n";
        var_dump($responseBody);
        echo "---End Response Body---\n";
    $transport->close();
} catch (Exception\Exception $e) {
    var_dump($e->getCode());
    var_dump($e->getMessage());
} catch (Exception\RuntimeException $e) {
    var_dump($e->getCode());
    var_dump($e->getMessage());
}