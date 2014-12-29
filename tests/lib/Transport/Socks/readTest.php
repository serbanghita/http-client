<?php
namespace HttpClientTest\Transport\Socks;

use HttpClient\Message\Request;
use \HttpClient\Transport\Socks;
use \HttpClient\Transport\Exception;

class ReadTest extends \PHPUnit_Framework_TestCase
{
	/**
	 * reading a stream with headers and body returns the expected request body
	 */
	public function testReadingAStreamWithHeadersAndBodyReturnsTheExpectedRequestBody()
	{
        $mockResponseBody = '{"result":{"id":819,"page":null},"error":null,"id":5}';
        $mockResponseArray = array(
            'Status: 200 OK' . "\r\n",
            'Content-Type: application/json' . "\r\n",
            'Content-length: ' . strlen($mockResponseBody) . "\r\n",
            'Connection: close' . "\r\n",
            "\r\n",
            $mockResponseBody
        );

        $stub = $this->getMockBuilder('\HttpClient\Transport\Socks')
                ->setMethods(array('getHandler', 'getRequest', 'readStreamLine', 'getStreamPosition'))
                ->getMock();

        $stub->expects($this->any())
            ->method('getHandler')
            ->willReturn('theHandlerResource');


        $stub->method('getRequest')->willReturn(new Request());

        $stub->expects($this->exactly(8))
            ->method('readStreamLine')
            ->will(
                $this->onConsecutiveCalls(
                    $mockResponseArray[0],
                    $mockResponseArray[1],
                    $mockResponseArray[2],
                    $mockResponseArray[3],
                    $mockResponseArray[4],
                    $mockResponseArray[5]
                )
            );

        $stub->expects($this->exactly(2))
            ->method('getStreamPosition')
            ->will(
                $this->onConsecutiveCalls(
                    strlen(implode('', array_slice($mockResponseArray, 0, 4))),
                    0
                )
            );

        $response = $stub->read();

        //var_dump($response);

	}

	
}
