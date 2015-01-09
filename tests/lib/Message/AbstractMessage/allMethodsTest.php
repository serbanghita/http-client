<?php
namespace HttpClientTest\Message\AbstractMessage;

use HttpClient\Message\AbstractMessage;
use HttpClient\Message\Headers;

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
		$inputHeadersArray = array(
			'Host' => 'test.mysite.local',
			'Connection' => 'close',
			'User-Agent' => 'Mozilla/5.0',
			'Accept-Language' => 'de,en;q=0.7,en-us;q=0.3'
		);
		$inputHeadersObj = new Headers($inputHeadersArray);
		$mock = $this->getMockForAbstractClass('\HttpClient\Message\AbstractMessage');
		$mock->setHeaders($inputHeadersObj);

		$this->assertEquals($inputHeadersObj, $mock->getHeaders());
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
	
}
