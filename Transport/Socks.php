<?php
namespace GenericApiClient\Transport;

use GenericApiClient\Transport\Exception;

class Socks extends AbstractTransport implements TransportInterface
{
    protected $options = array(
        'timeout' => 5,
        'follow_location' => false,
        'max_redirects' => 1
    );

    // @todo Refactor these.
    protected $gotResponseHeaders = false;
    protected $responseHeaders = array();

    public function connect()
    {
        // Defaults.
        $errno = null;
        $errstr = null;
        $flags = \STREAM_CLIENT_CONNECT | \STREAM_CLIENT_PERSISTENT;

        // Create the stream context.
        $context = stream_context_create();

        // Apply stream options.
        stream_context_set_option($context, 'http', 'timeout', $this->getOption('timeout'));
        stream_context_set_option($context, 'http', 'follow_location', $this->getOption('follow_location'));
        stream_context_set_option($context, 'http', 'max_redirects', $this->getOption('max_redirects'));

        // Create the handler. We use this in the request and response.
        $this->handler = @stream_socket_client(
            $this->getProtocol() . '://' . $this->getHost() . ':' . $this->getPort(),
            $errno,
            $errstr,
            0,
            $flags,
            $context
        );

        if (!$this->handler) {
            $this->close();
            throw new Exception\RuntimeException('Cannot open stream connection.');
        }


        // @todo Incorporate these settings in the Options.
        stream_set_timeout($this->handler, 1);
        stream_set_blocking($this->handler, 1);

        return true;
    }

    public function send()
    {
        if (!$this->handler) {
            throw new Exception\RuntimeException('Trying to write but no connection is available.');
        }

        // Apply mandatory headers.
        $this->request()->addHeader('Host', $this->getHost());
        $this->request()->addHeader('Content-length', strlen($this->request()->getBody()));
        $this->request()->addHeader('Accept', '*/*');
        // @todo: Merge this when using persistent connection.
        $this->request()->addHeader('Connection', 'close');

        $send = fwrite($this->handler, $this->request()->__toString());

        print_r(stream_get_meta_data($this->handler));
        var_dump($send);

        if ($send === false) {
            throw new Exception\RuntimeException('Could not write the request.');
        }

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
                    $headersArray = $this->request()->convertHeadersToArray($headers);
                    $gotResponseHeaders = true;
                    echo "\n". '---Begin response HTTP headers---' . "\n";
                    // var_dump($headers);
                    var_dump($this->request()->getPath());
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