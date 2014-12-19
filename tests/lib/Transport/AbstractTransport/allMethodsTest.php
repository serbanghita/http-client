<?php
namespace HttpClientTest\Transport\AbstractTransport;

use \HttpClient\Transport\AbstractTransport;

class allMethodsTest extends \PHPUnit_Framework_TestCase
{
    /**
     * @covers HttpClient\Transport\AbstractTransport::__construct()
     */
    public function testConstructorSetsTheOptions()
    {
        $ac = new AbstractTransport;
    }



}