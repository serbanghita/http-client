<?php
namespace HttpClient\Message;

interface MessageInterface
{
    public function setStatusCode($statusCode);

    public function getStatusCode();

    public function setHeaders(array $headers);

    public function getHeaders();

    public function addHeader($headerName, $headerValue);

    public function removeHeader($headerName);

    public function setBody($body);

    public function getBody();
}