<?php
namespace HttpClient\Message;

class Headers
{
    public $headers = array();
    public $headersRaw = '';

    public function __construct($headers = null)
    {
        if (is_string($headers)) {
            $this->headersRaw = $headers;
            $this->headers = $this->convertStringToArray($headers);
        }

        if (is_array($headers)) {
            $this->headers = $headers;
        }
    }

    public function add($headerName, $headerValue)
    {
        $this->headers[$headerName] = $headerValue;
    }

    public function get($headerName)
    {
        return isset($this->headers[$headerName]) ? $this->headers[$headerName] : null;
    }

    public function remove($headerName)
    {
        unset($this->headers[$headerName]);
    }

    public function getAsArray()
    {
        return $this->headers;
    }

    public function getAsString()
    {
        $result = '';
        if( count($this->headers) > 0 ) {
            foreach ($this->headers as $headerName => $headerValue) {
                $result .= $headerName . ': ' . $headerValue . "\r\n";
            }
        }
        return $result;
    }

    protected function convertStringToArray($headersString)
    {
        $result = array();

        $headersArray = explode("\r\n", $headersString);
        if (count($headersArray) > 0) {
            foreach ($headersArray as $headerLineString) {
                $headerLineString = trim($headerLineString);
                if (!empty($headerLineString) && strpos($headerLineString, ':') !== false) {
                    $headerLineArray = explode(':', $headerLineString, 2);
                    $headerLineArray[0] = ucfirst(strtolower($headerLineArray[0]));
                    $result[$headerLineArray[0]] = ltrim($headerLineArray[1]);
                }
            }
        }

        return $result;
    }
}