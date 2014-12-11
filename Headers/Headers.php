<?php
namespace GenericApiClient\Transport\Headers;

class Headers
{
    public $headersArray = array();

    /**
     * @param mixed $headers Headers passed can be string or arrays.
     */
    public function __construct($headers)
    {
        if (is_string($headers)) {
            $this->headersArray = $this->parse($headers);
        }

        if (is_array($headers)) {
            $this->headersArray = $headers;
        }
    }

    public function addHeader($headerName, $headerValue)
    {
        $this->headersArray[$headerName] = $headerValue;
    }

    public function removeHeader($headerName)
    {
        unset($this->headersArray[$headerName]);
    }

    public function getAsArray()
    {
        return $this->headersArray;
    }

    public function getAsString()
    {
        return $this->flatten($this->headersArray);
    }

    /**
     * @param string $headersString
     * @return array
     */
    protected function parse($headersString)
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

    protected function flatten($headersArray = array())
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