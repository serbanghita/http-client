<?php
namespace GenericApiClient\Transport;

use GenericApiClient\Transport\Request\Request as Request;

class Socks implements TransportInterface
{
    public $options;
    public $request;

    protected $handler;

    protected $gotResponseHeaders = false;
    protected $responseHeaders = array();


    public function getResponseHeader($headerName)
    {
        return $this->responseHeaders[$headerName];
    }

    public function connect(Options $options)
    {
        // Store the options for later use.
        $this->options = $options;

        // Defaults.
        $errno = null;
        $errstr = null;
        $flags = \STREAM_CLIENT_CONNECT | \STREAM_CLIENT_PERSISTENT;

        // Create the stream context.
        $context = stream_context_create();

        // Apply stream options.
        stream_context_set_option($context, 'http', 'timeout', $this->options->timeout);
        stream_context_set_option($context, 'http', 'follow_location', $this->options->follow_location);
        stream_context_set_option($context, 'http', 'max_redirects', $this->options->max_redirects);

        // Create the handler. We use this in the request and response.
        $this->handler = @stream_socket_client(
            $this->options->protocol . '://' . $this->options->host . ':' . $this->options->port,
            $errno,
            $errstr,
            0,
            $flags,
            $context
        );

        if (!$this->handler) {
            throw new \RuntimeException($errstr, $errno);
        }

        stream_set_timeout($this->handler, 1);
        stream_set_blocking($this->handler, 1);

        return true;
    }

    public function send(Request $request)
    {
        if (!$this->handler) {
            throw new \RuntimeException('Trying to write but no connection was initiated.');
        }

        print_r($request->getAsString());
        exit;

        $send = fwrite($this->handler, $request->getAsString());

        // var_dump($send);
        // print_r(stream_get_meta_data($this->handler));

        if ($send === false) {
            throw new \RuntimeException('Could not write the request.');
        }

        var_dump($send);

        return true;
    }

    public function read()
    {
        $headers = '';
        $headersArray = array();
        $gotResponseHeaders = false;
        $response = '';
        while (($line = fgets($this->handler)) !== false) {
            // print_r(stream_get_meta_data($this->handler));
            // Read the headers of the current response.
            if (!$gotResponseHeaders) {
                $headers .= $line;
                if (rtrim($line) === '') {
                    $headersArray = $this->parseHeaders($headers);
                    $gotResponseHeaders = true;
                    echo "\n". '---Begin response HTTP headers---' . "\n";
                    // var_dump($headers);
                    var_dump($path);
                    echo "---End response HTTP headers---\n\n";
                }
            } else {
                $currentPosition = ftell($this->handler);
                $bodyLength = isset($headersArray['Content-length']) ? (int)$headersArray['Content-length'] : 0;

                $response .= $line;

                if ($bodyLength>0) {
                    $maxReadLength = $bodyLength + $currentPosition;
                    if($currentPosition > $maxReadLength) {
                        break;
                    }
                } else {
                    if (feof($this->handler)) {
                        break;
                    }
                }

            }
        }

        // Check for the Connection: close header.
        $connection = isset($headersArray['Connection']) ? $headersArray['Connection'] : null;
        if ($connection == 'close') {
            $this->close();
        }

        echo "\n" . '---Begin Response---' . "\n";
        var_dump($response);
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