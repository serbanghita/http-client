<?php
namespace HttpClient\Transport;

use HttpClient\Transport\Exception;

class Socks extends AbstractTransport implements TransportInterface
{
    protected $options = array(
        'timeout' => 30,
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

        // Perform basic checks.
        if (!$this->getHost()) {
            throw new Exception\RuntimeException('No host to connect to.');
        }

        // Create the stream context.
        $context = stream_context_create();

        // Apply stream options.
        stream_context_set_option($context, 'http', 'timeout', $this->getOption('timeout'));
        stream_context_set_option($context, 'http', 'follow_location', $this->getOption('follow_location'));
        stream_context_set_option($context, 'http', 'max_redirects', $this->getOption('max_redirects'));
        if ($this->getProxy()) {
            stream_context_set_option($context, 'http', 'proxy', $this->getProtocol() . '://' . $this->getProxy());
            stream_context_set_option($context, 'http', 'request_fulluri', true);
        }

        //var_dump(file_get_contents('http://demo.mobiledetect.net/test/jsonrpc.php?page=test', false, $context));
        //print_r(stream_context_get_options($context));

        //$ipAddress = gethostbyname($this->getHost());
        // Create the handler. We use this in the request and response.
        $this->handler = $this->openSocket(
            $this->getProtocol() . '://' . $this->getHost() . ':' . $this->getPort(),
            $errno,
            $errstr,
            30,
            $flags,
            $context
        );

        if (!$this->handler) {
            $this->close();
            throw new Exception\RuntimeException(
                sprintf('Cannot open stream connection. [Reason: %s] [Code: %d]', $errstr, $errno)
            );
        }

        // @todo Incorporate these settings in the Options.
        stream_set_timeout($this->handler, 5);
        stream_set_blocking($this->handler, 1);

        return true;
    }

    public function openSocket($remote_socket, &$errno, &$errstr, $timeout, $flags)
    {
        $handler = \stream_socket_client($remote_socket, $errno, $errstr, $timeout, $flags);
        return $handler;
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

        //print_r(stream_get_meta_data($this->handler));
        //var_dump($send);

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
                    if ($currentPosition > $maxReadLength) {
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
            stream_set_blocking($this->handler, 0);
            fclose($this->handler);
            return true;
        } else {
            return false;
        }
    }
}