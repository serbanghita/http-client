<?php
namespace HttpClientTest\Transport\Socks;

use \HttpClient\Transport\Socks;
use \HttpClient\Transport\Exception;

class ConnectTest extends \PHPUnit_Framework_TestCase
{
    /**
     * if host is not set connect will throw an error
     * @expectedException \HttpClient\Transport\Exception\RuntimeException
     * @expectedExceptionCode \HttpClient\Transport\Socks::INVALID_HOST
     */
    public function testIfHostIsNotSetConnectWillThrowAnError()
    {
        $transport = new Socks;
        $transport->connect();
    }

    /**
     * if stream socket cannot be opened connect with throw an error
     * @expectedException \HttpClient\Transport\Exception\RuntimeException
     * @expectedExceptionCode \HttpClient\Transport\Socks::ERROR_OPENING_STREAM
     */
    public function testIfStreamSocketCannotBeOpenedConnectWithThrowAnError()
    {
        $stub = $this->getMockBuilder('\HttpClient\Transport\Socks')
            ->setMethods(array('getHost', 'createStreamContext', 'openStream', 'close'))
            ->getMock();

        $stub->method('getHost')->willReturn('www.test.local');
        $stub->method('createStreamContext')->willReturn(array());
        $stub->method('openStream')->willReturn(false);
        $stub->method('close')->willReturn(false);

        $stub->connect();
    }

    /**
     * if stream socket is opened successfully connect will return true
     */
    public function testIfStreamSocketIsOpenedSuccessfullyConnectWillReturnTrue()
    {
        $stub = $this->getMockBuilder('\HttpClient\Transport\Socks')
            ->setMethods(array('getHost', 'createStreamContext', 'openStream', 'close'))
            ->getMock();

        $stub->method('getHost')->willReturn('www.test.local');
        $stub->method('createStreamContext')->willReturn(array());
        $stub->method('openStream')->willReturn(true);

        $this->assertTrue($stub->connect());
    }

    

}
