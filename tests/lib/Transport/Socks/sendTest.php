<?php
namespace HttpClientTest\Transport\Socks;

use HttpClient\Transport\Socks;
use HttpClient\Transport\Exception;

class SendTest extends \PHPUnit_Framework_TestCase
{
	/**
	 * calling send without a valid handler throws an exception
     * @expectedException \HttpClient\Transport\Exception\RuntimeException
     * @expectedExceptionCode \HttpClient\Transport\Socks::INVALID_HANDLER
	 */
	public function testCallingSendWithoutAValidHandlerThrowsAnException()
	{
        $stub = $this->getMockBuilder('\HttpClient\Transport\Socks')
            ->setMethods(array('connect'))
            ->getMock();
        $stub->method('connect')->willReturn(true);

        $stub->send();
	}

	/**
	 * writing to stream and failing throws an exception
     * @expectedException \HttpClient\Transport\Exception\RuntimeException
     * @expectedExceptionCode \HttpClient\Transport\Socks::ERROR_WRITING_TO_STREAM
	 */
	public function testWritingToStreamAndFailingThrowsAnException()
	{
        $stub = $this->getMockBuilder('\HttpClient\Transport\Socks')
            ->setMethods(array('connect', 'getHandler', 'setStreamTimeout', 'setStreamBlockingMode', 'writeToStream'))
            ->getMock();
        $stub->method('connect')->willReturn(true);
        $stub->method('getHandler')->willReturn(true);
        $stub->method('setStreamTimeout')->willReturn(true);
        $stub->method('setStreamBlockingMode')->willReturn(true);
        $stub->method('writeToStream')->willReturn(false);

        $stub->send();
	}

	
	/**
	 * writing successfully to stream returns true
	 */
	public function testWritingSuccessfullyToStreamReturnsTrue()
	{
        $stub = $this->getMockBuilder('\HttpClient\Transport\Socks')
            ->setMethods(array('connect', 'getHandler', 'setStreamTimeout', 'setStreamBlockingMode', 'writeToStream'))
            ->getMock();
        $stub->method('connect')->willReturn(true);
        $stub->method('getHandler')->willReturn(true);
        $stub->method('setStreamTimeout')->willReturn(true);
        $stub->method('setStreamBlockingMode')->willReturn(true);
        $stub->method('writeToStream')->willReturn(true);

        $this->assertTrue($stub->send());
	}

	
	
}
