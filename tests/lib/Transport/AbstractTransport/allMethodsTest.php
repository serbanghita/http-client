<?php
namespace HttpClientTest\Transport\AbstractTransport;

use \HttpClient\Transport\AbstractTransport;

class AllMethodsTest extends \PHPUnit_Framework_TestCase
{

    /**
     *  constructor sets the given input options
     */
    public function testConstructorSetsTheGivenInputOptions()
    {
        $inputOptions = array('test1' => 'val1', 'test2' => 'val2');
        $stub = $this->getMockForAbstractClass('\HttpClient\Transport\AbstractTransport', array($inputOptions));

        $this->assertEquals($inputOptions, $stub->getOptions());
    }

    /**
     * set host sets the desired host
     */
    public function testSetHostSetsTheDesiredHost()
    {
        $inputHost = 'www.google.com';
        $mock = $this->getMockForAbstractClass('\HttpClient\Transport\AbstractTransport');
        $mock->setHost($inputHost);

        $this->assertEquals($inputHost, $mock->getHost());
    }

    /**
     * set port sets the desired port
     */
    public function testSetPortSetsTheDesiredPort()
    {
        $inputPort = 8080;
        $mock = $this->getMockForAbstractClass('\HttpClient\Transport\AbstractTransport');
        $mock->setPort($inputPort);

        $this->assertEquals($inputPort, $mock->getPort());
    }

    /**
     * set protocol sets the desired protocol
     */
    public function testSetProtocolSetsTheDesiredProtocol()
    {
        $inputProtocol = 'ssl';
        $mock = $this->getMockForAbstractClass('\HttpClient\Transport\AbstractTransport');
        $mock->setProtocol($inputProtocol);

        $this->assertEquals($inputProtocol, $mock->getProtocol());
    }

    /**
     * set proxy sets the desired proxy
     */
    public function testSetProxySetsTheDesiredProxy()
    {
        $inputProxy = 'proxy.myserver.local:8080';
        $mock = $this->getMockForAbstractClass('\HttpClient\Transport\AbstractTransport');
        $mock->setProxy($inputProxy);

        $this->assertEquals($inputProxy, $mock->getProxy());
    }

    /**
     * add option adds the options to the options array
     */
    public function testAddOptionAddsTheOptionsToTheOptionsArray()
    {
        $mock = $this->getMockForAbstractClass('\HttpClient\Transport\AbstractTransport');
        $mock->addOption('firstOptionName', 'firstOptionValue');

        $this->assertEquals('firstOptionValue', $mock->getOption('firstOptionName'));
    }


    /**
     * get options returns the previous set options
     */
    public function testGetOptionsReturnsThePreviousSetOptions()
    {
        $mock = $this->getMockForAbstractClass('\HttpClient\Transport\AbstractTransport');
        $mock->addOption('firstOptionName', 'firstOptionValue');
        $mock->addOption('secondOptionName', 'secondOptionValue');

        $this->assertArrayHasKey('firstOptionName', $mock->getOptions());
        $this->assertArrayHasKey('secondOptionName', $mock->getOptions());
    }

    /**
     * create request creates a new request object
     */
    public function testCreateRequestCreatesANewRequestObject()
    {
        $mock = $this->getMockForAbstractClass('\HttpClient\Transport\AbstractTransport');
        $mock->createRequest();

        $this->assertInstanceOf('\HttpClient\Message\Request', $mock->getRequest());
    }

    /**
     * request method creates and returns a request object
     */
    public function testRequestMethodCreatesAndReturnsARequestObject()
    {
        $mock = $this->getMockForAbstractClass('\HttpClient\Transport\AbstractTransport');
        $request = $mock->request();
        $this->assertInstanceOf('\HttpClient\Message\Request', $request);
        $request2 = $mock->request();
        $this->assertSame($request, $request2);
    }

    /**
     * response method creates and returns a response object
     */
    public function testResponseMethodCreatesAndReturnsAResponseObject()
    {
        $mock = $this->getMockForAbstractClass('\HttpClient\Transport\AbstractTransport');
        $response = $mock->response();
        $this->assertInstanceOf('\HttpClient\Message\Response', $response);
        $response2 = $mock->response();
        $this->assertSame($response, $response2);
    }

}
