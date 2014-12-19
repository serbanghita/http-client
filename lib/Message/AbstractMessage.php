<?php
namespace HttpClient\Message;

abstract class AbstractMessage implements MessageInterface
{
    protected $statusCode = 0;
    protected $headers = array();
    protected $body;

    public function setStatusCode($statusCode)
    {
        $this->statusCode = $statusCode;
    }

    public function getStatusCode()
    {
        return $this->statusCode;
    }

    public function setHeaders(array $headers)
    {
        $this->headers = $headers;
    }

    public function getHeaders()
    {
        return $this->headers;
    }

    public function addHeader($headerName, $headerValue)
    {
        $this->headers[$headerName] = $headerValue;
    }

    public function removeHeader($headerName)
    {
        unset($this->headers[$headerName]);
    }

    public function setBody($body)
    {
        $this->body = $body;
    }

    public function getBody()
    {
        return $this->body;
    }

    /**
     *
     * Utility methods
     *
    */

    /**
     * @param $headersString
     * @return array
     */
    public function convertHeadersToArray($headersString)
    {
        $result = array();

        $headersArray = explode("\r\n", $headersString);
        if (count($headersArray) > 0) {
            foreach ($headersArray as $headerLineString) {
                $headerLineString = trim($headerLineString);
                if (!empty($headerLineString) && strpos($headerLineString, ':') !== false) {
                    $headerLineArray = explode(':', $headerLineString, 2);
                    $headerLineArray[0] = ucfirst(strtolower($headerLineArray[0]));
                    $result[$headerLineArray[0]] = $headerLineArray[1];
                }
            }
        }

        return $result;
    }

    /**
     * @param array $headersArray
     * @return bool|string
     */
    public function convertHeadersToString($headersArray = array())
    {
        if (!is_array($headersArray) || empty($headersArray)) {
            return false;
        }
        $result = '';
        foreach ($headersArray as $headerName => $headerValue) {
            $result .= $headerName . ': ' . $headerValue . "\r\n";
        }
        return $result;
    }

}