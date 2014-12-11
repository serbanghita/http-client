<?php
namespace GenericApiClient\Transport\Request;

class Options
{
    public $http_version = '1.1';
    public $method = 'GET';
    public $path = null;
    public $body = '';
}