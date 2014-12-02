<?php
namespace GenericApiClient\Transport;
interface TransportInterface
{
    public function connect($host, $port = 80);
    public function send($method, $path = null, $requestBody = '');
    public function close();
}