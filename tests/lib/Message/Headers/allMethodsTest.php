<?php
namespace HttpClientTest\Message\Headers;

use HttpClient\Message\Headers;

class AllMethodsTest extends \PHPUnit_Framework_TestCase
{
	/**
	 * get headers as array returns an empty array if invalid headers are set
	 */
	public function testGetHeadersAsArrayReturnsAnEmptyArrayIfInvalidHeadersAreSet()
	{
        $mock = new Headers('');
        $this->assertEquals(array(), $mock->getAsArray());

        $mock = new Headers(111);
        $this->assertEquals(array(), $mock->getAsArray());

        $mock = new Headers(true);
        $this->assertEquals(array(), $mock->getAsArray());
	}

    /**
     * get headers as string returns an empty string if invalid headers are set
     */
	public function testGetHeadersAsStringReturnsAnEmptyStringIfInvalidHeadersAreSet()
    {
        $mock = new Headers('');
        $this->assertEmpty($mock->getAsString());

        $mock = new Headers(null);
        $this->assertEmpty($mock->getAsString());

        $mock = new Headers(false);
        $this->assertEmpty($mock->getAsString());
    }

	/**
	 * constructor sets the headers array if they are passed correctly as an array
	 */
	public function testConstructorSetsTheHeadersArrayIfTheyArePassedCorrectlyAsAnArray()
	{
        $inputHeadersArray = array(
            'Host' => 'test.mysite.local',
            'Connection' => 'close'
        );
        $mock = new Headers($inputHeadersArray);
        $this->assertEquals($inputHeadersArray, $mock->getAsArray());
	}

	/**
	 * constructor sets the headers array if they are passed correctly as a string
	 */
	public function testConstructorSetsTheHeadersArrayIfTheyArePassedCorrectlyAsAString()
	{
        $inputHeadersString = 'GET /test/jsonrpc.php HTTP/1.1' . "\r\n" .
            'Host: test.mysite.local' . "\r\n" .
            'Connection: close' . "\r\n";
        $expectedHeadersArray = array(
            'Host' => 'test.mysite.local',
            'Connection' => 'close'
        );
        $mock = new Headers($inputHeadersString);
        $this->assertEquals($expectedHeadersArray, $mock->getAsArray());
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
        $mock = new Headers($inputHeaders);
        $mock->add($inputHeaderName, $inputHeaderValue);

        $this->assertEquals($inputHeaderValue, $mock->get($inputHeaderName));
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
        $mock = new Headers($inputHeaders);

        $mock->remove($inputHeaderName);
        $this->assertNull($mock->get($inputHeaderName));
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
        $mock = new Headers($inputHeadersString);
        $headersArray = $mock->getAsArray();

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

        $mock = new Headers($inputHeadersArray);
        $headersString = $mock->getAsString();

        $this->assertEquals($expectedHeadersString, $headersString);
    }
}
