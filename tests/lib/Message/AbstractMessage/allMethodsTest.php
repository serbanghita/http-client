<?php
namespace HttpClientTest\Message\AbstractMessage;

use HttpClient\Message\AbstractMessage;

class AllMethodsTest extends \PHPUnit_Framework_TestCase
{
	/**
	 * setting status code returns the expected status code
	 */
	public function testSettingStatusCodeReturnsTheExpectedStatusCode()
	{
        $inputCode = 200;
        $mock = $this->getMockForAbstractClass('\HttpClient\Message\AbstractMessage');
        $mock->setStatusCode($inputCode);

        $this->assertEquals($inputCode, $mock->getStatusCode());
	}

	/**
	 * setting the headers array returns the expected headers
	 */
	public function testSettingTheHeadersArrayReturnsTheExpectedHeaders()
	{
		$inputHeaders = array(
			'Host' => 'test.mysite.local',
			'Connection' => 'close',
			'User-Agent' => 'Mozilla/5.0',
			'Accept-Language' => 'de,en;q=0.7,en-us;q=0.3'
		);
		$mock = $this->getMockForAbstractClass('\HttpClient\Message\AbstractMessage');
		$mock->setHeaders($inputHeaders);

		$this->assertEquals($inputHeaders, $mock->getHeaders());
	}

	/**
	 * adding a new header is found the the headers array
	 */
	public function testAddingANewHeaderIsFoundTheTheHeadersArray()
	{
		$inputHeaders = array(
			'Host' => 'test.mysite.local'
		);
		$inputHeaderName = 'Connection';
		$inputHeaderValue = 'close';
		$mock = $this->getMockForAbstractClass('\HttpClient\Message\AbstractMessage');
		$mock->setHeaders($inputHeaders);
		$mock->addHeader($inputHeaderName, $inputHeaderValue);

		$this->assertEquals($inputHeaderValue, $mock->getHeader($inputHeaderName));
	}

	/**
	 * removing header works and is no longer in the headers array
	 */
	public function testRemovingHeaderWorksAndIsNoLongerInTheHeadersArray()
	{
		$inputHeaders = array(
			'Host' => 'test.mysite.local',
			'Connection' => 'close'
		);
		$inputHeaderName = 'Connection';
		$mock = $this->getMockForAbstractClass('\HttpClient\Message\AbstractMessage');
		$mock->setHeaders($inputHeaders);

		$mock->removeHeader($inputHeaderName);
		$this->assertNull($mock->getHeader($inputHeaderName));
	}

	/**
	 * setting a specific request body returns the expected body
	 */
	public function testSettingASpecificRequestBodyReturnsTheExpectedBody()
	{
		$inputBody = '{"action":"editProduct"}';
		$mock = $this->getMockForAbstractClass('\HttpClient\Message\AbstractMessage');
		$mock->setBody($inputBody);

		$this->assertEquals($inputBody, $mock->getBody());
	}

	/**
	 * converting a plain text headers list to array returns the expected headers
	 */
	public function testConvertingAPlainTextHeadersListToArrayReturnsTheExpectedHeaders()
	{
		$inputHeadersString = 'GET /test/jsonrpc.php HTTP/1.1' . "\r\n" .
								'Host: test.mysite.local' . "\r\n" .
								'Connection: close' . "\r\n";
		$expectedHeadersArray = array(
			'Host' => 'test.mysite.local',
			'Connection' => 'close'
		);
		$mock = $this->getMockForAbstractClass('\HttpClient\Message\AbstractMessage');
		$headersArray = $mock->convertHeadersToArray($inputHeadersString);

		$this->assertEquals($headersArray, $expectedHeadersArray);

	}

	/**
	 * converting an array of headers to string returns the expected output
	 */
	public function testConvertingAnArrayOfHeadersToStringReturnsTheExpectedOutput()
	{
		$inputHeadersArray = array(
			'Host' => 'test.mysite.local',
			'Connection' => 'close'
		);
		$expectedHeadersString = 'Host: test.mysite.local' . "\r\n" .
			'Connection: close' . "\r\n";

		$mock = $this->getMockForAbstractClass('\HttpClient\Message\AbstractMessage');
		$headersString = $mock->convertHeadersToString($inputHeadersArray);

		$this->assertEquals($expectedHeadersString, $headersString);
	}

	/**
	 * converting invalid headers returns false
	 */
	public function testConvertingInvalidHeadersReturnsFalse()
	{
		$mock = $this->getMockForAbstractClass('\HttpClient\Message\AbstractMessage');

		$this->assertFalse($mock->convertHeadersToString(''));
		$this->assertFalse($mock->convertHeadersToString(null));
		$this->assertFalse($mock->convertHeadersToString(false));
	}

	
	
}
