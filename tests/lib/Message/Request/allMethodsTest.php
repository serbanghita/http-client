<?php
namespace HttpClientTest\Message\Request;

use HttpClient\Message\Headers;
use \HttpClient\Message\Request;

class AllMethodsTest extends \PHPUnit_Framework_TestCase
{
	/**
	 * setting http version returns the expected http version
	 */
	public function testSettingHttpVersionReturnsTheExpectedHttpVersion()
	{
        $inputHttpVersion = '1.1';
        $request = new Request();
        $request->setHttpVersion($inputHttpVersion);

        $this->assertEquals($inputHttpVersion, $request->getHttpVersion());
	}

	/**
	 * setting request method type returns the expected method
	 */
	public function testSettingRequestMethodTypeReturnsTheExpectedMethod()
	{
        $inputMethod = 'GET';
        $request = new Request();
        $request->setMethod($inputMethod);

        $this->assertEquals($inputMethod, $request->getMethod());
	}

	/**
	 * setting the request path returns the expected path
	 */
	public function testSettingTheRequestPathReturnsTheExpectedPath()
	{
        $inputPath = 'test/index.php';
        $request = new Request();
        $request->setPath($inputPath);

        $this->assertEquals($inputPath, $request->getPath());
	}

	/**
	 * converting the request object to string returns the expected headers and body output
	 */
	public function testConvertingTheRequestObjectToStringReturnsTheExpectedHeadersAndBodyOutput()
	{
        $mockedHeadersString = 'Host: test.mysite.local' . "\r\n" .
        'Connection: close' . "\r\n";
        $headers = new Headers($mockedHeadersString);
        $mock = $this->getMockBuilder('\HttpClient\Message\Request')
                ->setConstructorArgs(array($headers))
                ->setMethods(array('convertHeadersToString', 'getBody'))
                ->getMock();

        $inputBody = 'this is the body';
        $inputMethod = 'GET';
        $inputPath = '/test/index.php';
        $inputHttpVersion = '1.0';

        $mock->method('getBody')->willReturn($inputBody);
        $mock->setMethod($inputMethod);
        $mock->setPath($inputPath);
        $mock->setHttpVersion($inputHttpVersion);

        $expectedString = $inputMethod . ' ' . $inputPath . ' HTTP/' . $inputHttpVersion . "\r\n";
        $expectedString .= $headers->getAsString();
        $expectedString .= "\r\n";
        $expectedString .= $inputBody;

        $this->assertEquals($expectedString, (string)$mock);
	}
}
