Generic HTTP Client
===================
> A basic HTTP client used for talking with APIs.

[![Build Status](https://travis-ci.org/serbanghita/http-client.svg?branch=master)](https://travis-ci.org/serbanghita/http-client)
[![Coverage Status](https://coveralls.io/repos/serbanghita/http-client/badge.png)](https://coveralls.io/r/serbanghita/http-client)
[![Code Climate](https://codeclimate.com/github/serbanghita/http-client/badges/gpa.svg)](https://codeclimate.com/github/serbanghita/http-client)

### Example

```php
$transport = new \HttpClient\Transport\Socks();
$transport->setHost('http-client.serbang');

try {
    $transport->connect();
    $transport->request()->setPath('/tests/providers/response/text-chunked.php');
    $transport->request()->setBody(generateRandomString(10));
    $transport->request()->headers()->add('Connection', 'keep-alive');
    $transport->request()->headers()->add('Content-type', 'application/json');
    $transport->send();
    $transport->read();
    $transport->close();

    // Debug.
    echo "\n" . '---Begin Headers Body---' . "\n";
    var_dump($transport->getResponse()->getHeaders());
    echo "---End Headers Body---\n";
    echo "\n" . '---Begin Response Body---' . "\n";
    var_dump($transport->getResponse()->getBody());
    echo "---End Response Body---\n";

} catch (\HttpClient\Transport\Exception\Exception $e) {
    var_dump($e->getCode());
    var_dump($e->getMessage());
} catch (\HttpClient\Transport\Exception\RuntimeException $e) {
    var_dump($e->getCode());
    var_dump($e->getMessage());
}
```


External references:

* https://github.com/reactphp/socket/blob/master/src/Connection.php
* https://github.com/stage1/docker-php/blob/master/src/Docker/Http/Adapter/DockerAdapter.php
* https://github.com/zendframework/zf2/blob/master/library/Zend/Http/Client/Adapter/Socket.php
* http://stackoverflow.com/questions/10449540/reading-data-from-fsockopen-using-fgets-fread-hangs
