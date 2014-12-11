<?php
namespace GenericApiClient\Transport;

/**
 * Class Options
 * @package GenericApiClient\Transport
 */
class Options
{
    public $host;
    public $port;
    public $protocol = 'tcp';
    public $user_agent = 'Generic API Client';
    public $persistent = false;
    public $follow_location = false;
    public $max_redirects = 1;
    public $timeout = 5;
}