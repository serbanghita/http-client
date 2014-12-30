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
} catch (\HttpClient\Transport\Exception\Exception $e) {
    var_dump($e->getCode());
    var_dump($e->getMessage());
} catch (\HttpClient\Transport\Exception\RuntimeException $e) {
    var_dump($e->getCode());
    var_dump($e->getMessage());
}