<?php
namespace HttpClient\Transport;

use HttpClient\Message\AbstractMessage;

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
    public function setHandler($handler);
    public function getHandler();
    public function connect();
    public function send(AbstractMessage $message = null);
    public function read();
    public function close();
}