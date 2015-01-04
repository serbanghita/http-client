<?php
namespace HttpClient\Transport;

use HttpClient\Message\Request;
use HttpClient\Message\Response;

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
        if (!is_null($transportOptions)) {
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

    public function createRequest()
    {
        $this->request = new Request();
    }

    /**
     * @return Request
     */
    public function getRequest()
    {
        return $this->request;
    }

    /**
     * @return Request
     */
    public function request()
    {
        if (!($this->getRequest() instanceof Request)) {
            $this->createRequest();
        }

        return $this->getRequest();
    }

    public function createResponse()
    {
        $this->response = new Response();
    }

    /**
     * @return Response
     */
    public function getResponse()
    {
        return $this->response;
    }

    /**
     * @return Response
     */
    public function response()
    {
        if (!($this->getResponse() instanceof Response)) {
            $this->createResponse();
        }

        return $this->getResponse();
    }

    public function setHandler($handler)
    {
        $this->handler = $handler;
    }

    public function getHandler()
    {
        return $this->handler;
    }
}