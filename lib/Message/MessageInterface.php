<?php
namespace HttpClient\Message;

interface MessageInterface
{
    public function setStatusCode($statusCode);
    public function getStatusCode();
    public function setHeaders(Headers $headers);
    public function getHeaders();
    public function setBody($body);
    public function getBody();
}