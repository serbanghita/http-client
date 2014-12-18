<?php
namespace GenericApiClient\Transport;

use GenericApiClient\Message\Request;

abstract class AbstractTransport implements TransportInterface
{
    protected $options = array();
    protected $host;
    protected $port = 80;
    protected $protocol = 'tcp';
    protected $proxy;
    protected $request;
    protected $response;
    protected $handler;

    public function __construct(array $transportOptions = null)
    {
        if(!is_null($transportOptions)) {
            $this->options = array_merge($this->options, $transportOptions);
        }
    }

    public function setHost($host)
    {
        $this->host = $host;
    }

    public function getHost()
    {
        return $this->host;
    }

    public function setPort($port)
    {
        $this->port = $port;
    }

    public function getPort()
    {
        return $this->port;
    }

    public function setProtocol($protocol)
    {
        $this->protocol = $protocol;
    }

    public function getProtocol()
    {
        return $this->protocol;
    }

    /**
     * @param string $proxy (e.g. proxy.server.local:8080)
     */
    public function setProxy($proxy)
    {
        $this->proxy = $proxy;
    }

    public function getProxy()
    {
        return $this->proxy;
    }

    public function addOption($optionName, $optionValue)
    {
        $this->options[$optionName] = $optionValue;
    }

    public function getOption($optionName)
    {
        return isset($this->options[$optionName]) ? $this->options[$optionName] : null;
    }

    public function getOptions()
    {
        return $this->options;
    }

    public function setRequest(array $options = null)
    {
        $this->request = new Request($options);
    }

    /**
     * @return Request
     */
    public function getRequest()
    {
        return $this->request;
    }

    /**
     * @param array $options
     * @return Request
     */
    public function request(array $options = null)
    {
        if (!($this->getRequest() instanceof Request)) {
            $this->setRequest($options);
        }

        return $this->getRequest();
    }
}