<?php
namespace HttpClient\Message;

class Request extends AbstractMessage implements MessageInterface
{
    protected $httpVersion  = '1.1';
    protected $method = 'GET';
    protected $path = null;

    /**
     * @var Headers
     */
    protected $headers;

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
        $requestString .= $this->headers()->getAsString();
        $requestString .= "\r\n";
        $requestString .= $this->getBody();

        return $requestString;
    }
}