Generic HTTP Client
===================
> A basic HTTP client used for talking with APIs.

[![Build Status](https://travis-ci.org/serbanghita/http-client.svg?branch=master)](https://travis-ci.org/serbanghita/http-client)
[![Coverage Status](https://coveralls.io/repos/serbanghita/http-client/badge.png)](https://coveralls.io/r/serbanghita/http-client)

### Example

```php
$transport = new Socks();
$transport->setHost('www.myhost.local');
//$transport->setProxy('proxy.myhost.local:8080');
try {
    $transport->connect();
    $transport->request()->setPath('/tests/providers/response/jsonrpc.php?page=' . generateRandomString(5));
    $transport->request()->setBody(generateRandomString(10));
    $transport->request()->addHeader('Content-type', 'application/json');
    $transport->send();
    $responseBody = $transport->read();
        echo "\n" . '---Begin Response Body---' . "\n";
        var_dump($responseBody);
        echo "---End Response Body---\n";
    $transport->close();
} catch (Exception\Exception $e) {
    var_dump($e->getCode());
    var_dump($e->getMessage());
} catch (Exception\RuntimeException $e) {
    var_dump($e->getCode());
    var_dump($e->getMessage());
}
```


External references:

* https://github.com/reactphp/socket/blob/master/src/Connection.php
* https://github.com/stage1/docker-php/blob/master/src/Docker/Http/Adapter/DockerAdapter.php
* https://github.com/zendframework/zf2/blob/master/library/Zend/Http/Client/Adapter/Socket.php
* http://stackoverflow.com/questions/10449540/reading-data-from-fsockopen-using-fgets-fread-hangs
