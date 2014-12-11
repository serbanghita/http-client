<?php
namespace GenericApiClient\Transport\Request;

use GenericApiClient\Transport\Headers\Headers as Headers;

class Request
{
    public $options;
    public $headers;

    public function __construct(Headers $headers, Options $options)
    {
        $this->options = $options;
        $this->headers = $headers;
    }

    public function getAsString()
    {
        $requestString = $this->options->method . ' ' . $this->options->path . ' HTTP/' . $this->options->http_version . "\r\n";
        $requestString .= $this->headers->getAsString();
        $requestString .= "\r\n";
        $requestString .= $this->options->body;

        return $requestString;
    }

}