<?php
namespace HttpClientTest\Transport\Socks;

use \HttpClient\Transport\Socks;
use \HttpClient\Transport\Exception;

class CloseTest extends \PHPUnit_Framework_TestCase
{
	/**
	 * if handler is invalid close the stream will return false
	 */
	public function testIfHandlerIsInvalidCloseTheStreamWillReturnFalse()
	{
        $mock = $this->getMockBuilder('\HttpClient\Transport\Socks')
            ->setMethods(array('streamHandlerIsValid'))
            ->getMock();
        $mock->method('streamHandlerIsValid')->willReturn(false);

        $this->assertFalse($mock->close());
	}

	/**
	 * if handler is a valid resource closing the stream will return true
	 */
	public function testIfHandlerIsAValidResourceClosingTheStreamWillReturnTrue()
	{
        $mock = $this->getMockBuilder('\HttpClient\Transport\Socks')
            ->setMethods(array('streamHandlerIsValid', 'shutDownStream', 'setStreamBlockingMode'))
            ->getMock();
        $mock->method('streamHandlerIsValid')->willReturn(true);
        $mock->method('shutDownStream')->willReturn(true);
        $mock->method('setStreamBlockingMode')->willReturn(true);

        $this->assertTrue($mock->close());
	}

	
}
