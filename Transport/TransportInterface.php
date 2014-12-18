<?php
namespace GenericApiClient\Transport;

interface TransportInterface
{
    public function setHost($host);
    public function getHost();
    public function setPort($port);
    public function getPort();
    public function setProtocol($protocol);
    public function getProtocol();
    public function setProxy($proxy);
    public function getProxy();
    public function connect();
    public function send();
    public function read();
    public function close();
}