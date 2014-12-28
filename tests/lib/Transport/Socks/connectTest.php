<?php
namespace HttpClientTest\Transport\Socks;

use \HttpClient\Transport\Socks;
use \HttpClient\Transport\Exception;

class ConnectTest extends \PHPUnit_Framework_TestCase
{
    /**
     * if host is not set connect will throw an error
     * @expectedException \HttpClient\Transport\Exception\RuntimeException
     */
    public function testIfHostIsNotSetConnectWillThrowAnError()
    {
        $transport = new Socks;
        $transport->connect();
    }

    /**
     * if stream socket cannot be opened connect with throw an error
     * @expectedException \HttpClient\Transport\Exception\RuntimeException
     */
    public function testIfStreamSocketCannotBeOpenedConnectWithThrowAnError()
    {
        $stub = $this->getMockBuilder('\HttpClient\Transport\Socks')
            ->setMethods(array('getHost', 'openSocket', 'close'))
            ->getMock();

        $stub->method('getHost')->willReturn('www.test.local');
        $stub->method('openSocket')->willReturn(false);
        $stub->method('close')->willReturn(false);

        $stub->connect();
    }



}
