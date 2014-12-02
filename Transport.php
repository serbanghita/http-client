<?php
namespace GenericApiClient\Transport;

class Socks implements TransportInterface
{
    protected $host;
    protected $port;
    protected $handler;

    private function flattenHeaders($headersArray = array())
    {
        if (!is_array($headersArray) || empty($headersArray)) {
            return false;
        }
        $result = '';
        foreach ($headersArray as $headerName => $headerValue) {
            $result .= $headerName . ': ' . $headerValue . "\r\n";
        }
        return $result;
    }

    public function connect($host, $port = 80)
    {
        // Store for later use.
        $this->host = $host;
        $this->port = $port;

        // Defaults.
        $errno = null;
        $errstr = null;
        $defaultHeaders = array(
            'Host' => $host,
            'User-Agent' => 'Mozilla/5.0 (iPhone; CPU iPhone OS 5_0 like Mac OS X) AppleWebKit/534.46 (KHTML, like Gecko) Version/5.1 Mobile/9A334 Safari/7534.48.3',
            'Accept' => 'text/javascript, application/javascript, application/ecmascript, application/x-ecmascript, */*; q=0.01',
            // http://en.wikipedia.org/wiki/HTTP_persistent_connection
            //'Connection' => 'keep-alive',
            // http://stackoverflow.com/questions/2773396/whats-the-content-length-field-in-http-header
            'Content-length' => 0
        );
        $protocol = 'tcp';
        $flags = \STREAM_CLIENT_CONNECT | \STREAM_CLIENT_PERSISTENT;
        // Create the stream context.
        $context = stream_context_create();
        // Apply stream options.
        //stream_context_set_option($context, 'http', 'timeout', 10);
        //stream_context_set_option($context, 'http', 'header', $this->flattenHeaders($defaultHeaders));
        //stream_context_set_option($context, 'http', 'follow_location', true);
        //stream_context_set_option($context, 'http', 'max_redirects', 1);

        $this->handler = stream_socket_client(
            $protocol . '://' . $host . ':' . $port,
            $errno,
            $errstr,
            30,
            $flags,
            $context
        );

        if (!$this->handler) {
            throw new \RuntimeException($errstr, $errno);
        }

        stream_set_blocking($this->handler, 0);

        return true;
    }

    public function send($method, $path = null, $requestBody = '')
    {
        if (!$this->handler) {
            throw new \RuntimeException('Trying to write but no connection was done.');
        }

        $headers = array(
            'Host' => $this->host,
            //'Connection' => 'keep-alive',
            'Content-length' => strlen($requestBody) + 1
        );

        $request = $method . ' ' . $path . ' HTTP/1.1' . "\r\n";
        $request .= $this->flattenHeaders($headers);
        $request .= "\r\n";
        $request .= $requestBody;

        echo '---Begin request---' . "\n";
        print_r($request);
        echo "\n---End request---\n";

        $send = fwrite($this->handler, $request);

        //var_dump($send);
        if ($send === false) {
            throw new \RuntimeException('Could not write the request.');
        }

        $response = null;
        while (!feof($this->handler)) {
            $line = fread($this->handler, 1024);
            if (empty($line)) {
                break;
            }
            $response .= $line;
            echo '.';
        }
        /*
                $i = 0;
                while (!feof($this->handler)) {

                    //$currentPos = ftell($this->handler);
                    //var_dump($currentPos);

                    $chunk = fgets($this->handler, 128);
                    //$chunk = stream_get_line($this->handler, 128);
                    var_dump($chunk);
                    //if ($chunk === false) {
                    //    break;
                    //}
                    $chunk = rtrim($chunk);
                    //if (empty($chunk)) {
                    //    break;
                    //}
                    $response .= $chunk;
                    if($i>10){ break; }
                    $i++;
                }
                //while($chunk = fgets($this->handler)) {
                //    var_dump($chunk);
                //    $response .= $chunk;
                //}
        */

        //var_dump($request);
        echo '---Begin Response---' . "\n";
        print_r($response);
        echo "---End response---\n\n\n\n";

        return true;
    }

    public function close()
    {
        if (is_resource($this->handler)) {
            // http://chat.stackoverflow.com/transcript/message/7727858#7727858
            stream_socket_shutdown($this->handler, STREAM_SHUT_RDWR);
            stream_set_blocking($this->handler, false);
            fclose($this->handler);
        }
    }
}