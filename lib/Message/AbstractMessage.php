<?php
namespace HttpClient\Message;

abstract class AbstractMessage implements MessageInterface
{
    protected $statusCode = 0;
    protected $httpVersion;
    /**
     * @var Headers
     */
    protected $headers;
    protected $body;
    protected $bodyChunked = array();

    public function __construct(Headers $headers = null)
    {
        if (is_null($headers)) {
            $this->headers = new Headers();
        } else {
            $this->headers = $headers;
        }
    }

    public function setStatusCode($statusCode)
    {
        $this->statusCode = $statusCode;
    }

    public function getStatusCode()
    {
        return $this->statusCode;
    }

    public function setHttpVersion($httpVersion)
    {
        $this->httpVersion = $httpVersion;
    }

    public function getHttpVersion()
    {
        return $this->httpVersion;
    }

    public function setHeaders(Headers $headers)
    {
        $this->headers = $headers;
    }

    public function getHeaders()
    {
        return $this->headers;
    }

    /**
     * @return Headers
     */
    public function headers()
    {
        return $this->headers;
    }

    public function addBodyChunk($bodyChunk)
    {
        $this->bodyChunked[] = $bodyChunk;
        $this->body .= $bodyChunk;
    }

    public function getCurrentBodyChunk()
    {
        return end($this->bodyChunked);
    }

    public function setBody($body)
    {
        $this->body = $body;
    }

    public function getBody()
    {
        return $this->body;
    }

}