<?php
namespace GenericApiClient\Transport;

interface TransportInterface
{
    public function connect();

    public function send();

    public function read();

    public function close();
}