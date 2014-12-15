<?php
namespace GenericApiClient\Transport;

class Request extends AbstractMessage
{
    protected $httpVersion  = '1.1';
    protected $method = 'GET';
    protected $path = null;

    protected $headers = array(
        'Host' => null,
        'Content-length' => 0,
        'Accept' => '*/*'
    );

    public function setHttpVersion($httpVersion)
    {
        $this->httpVersion = $httpVersion;
    }

    public function getHttpVersion()
    {
        return $this->httpVersion;
    }

    public function setMethod($method)
    {
        $this->method = $method;
    }

    public function getMethod()
    {
        return $this->method;
    }

    public function setPath($path)
    {
        $this->path = $path;
    }

    public function getPath()
    {
        return $this->path;
    }

    public function __toString()
    {
        $requestString = $this->getMethod() . ' ' . $this->getPath() . ' HTTP/' . $this->getHttpVersion() . "\r\n";
        $requestString .= $this->convertHeadersToString($this->getHeaders());
        $requestString .= "\r\n";
        $requestString .= $this->getBody();

        return $requestString;
    }
}