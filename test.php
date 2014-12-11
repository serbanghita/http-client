<?php
error_reporting(E_ALL);

include_once dirname(__FILE__) . '/TransportInterface.php';
include_once dirname(__FILE__) . '/Transport.php';

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
$client->connect('http-client.serbang', 80);
for ($i=0; $i<2; $i++) {

    $request = new \stdClass();
    $request->method = 'get' . generateRandomString(5);
    $request->params = array('id' => rand(0,1000));
    $request->id = rand(0,10);

    $client->send('GET', '/server.php?page=' . $request->method, json_encode($request));
}
$client->close();
exit('Good!');