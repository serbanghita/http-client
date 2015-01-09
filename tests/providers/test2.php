<?php
$composer = dirname(__FILE__) . '/../../vendor/autoload.php';
if (!file_exists($composer)) {
    throw new \RuntimeException("Please run 'composer install' first to set up autoloading. $composer");
}

/**
 * @var \Composer\Autoload\ClassLoader $autoloader
 */
$autoloader = include_once $composer;

\error_reporting(E_ALL);

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

$transport = new \HttpClient\Transport\Socks();
// http://demo.mobiledetect.net/test/jsonrpc.php
// http://http-client.serbang/server.php
$transport->setHost('http-client.serbang');
// $transport->setProxy('proxy.avangate.local:8080');
//$transport->setProtocol('ssl');
//$transport->setHost('demo.mobiledetect.net');

try {
    $transport->connect();
    $transport->request()->setPath('/tests/providers/response/text-chunked.php');
    //$transport->request()->setPath('/tests/providers/response/jsonrpc.php?page=' . generateRandomString(5));
    //$transport->request()->setPath('/tests/response/jsonrpc.php?page=' . generateRandomString(5));
    $transport->request()->setBody(generateRandomString(10));
    $transport->request()->headers()->add('Connection', 'keep-alive');
    //$transport->request()->addHeader('Content-type', 'application/json');
    $transport->send();
    $transport->read();
    $transport->close();

    echo "\n" . '---Begin Headers Body---' . "\n";
    var_dump($transport->getResponse()->getHeaders());
    echo "---End Headers Body---\n";
    echo "\n" . '---Begin Response Body---' . "\n";
    var_dump($transport->getResponse()->getBody());
    echo "---End Response Body---\n";

} catch (\HttpClient\Transport\Exception\Exception $e) {
    var_dump($e->getCode());
    var_dump($e->getMessage());
} catch (\HttpClient\Transport\Exception\RuntimeException $e) {
    var_dump($e->getCode());
    var_dump($e->getMessage());
}