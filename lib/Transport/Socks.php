<?php
namespace HttpClient\Transport;

use HttpClient\Message\AbstractMessage;
use HttpClient\Transport\Exception;

class Socks extends AbstractTransport implements TransportInterface
{
    const INVALID_HOST = 0;
    const ERROR_OPENING_STREAM = 1;
    const INVALID_HANDLER = 2;
    const ERROR_WRITING_TO_STREAM = 3;

    protected $options = array(
        'persistent' => false,
        'timeout' => 30,
        'follow_location' => false,
        'max_redirects' => 1,
        'request_timeout' => 5,
        'request_blocking_mode' => true
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
            throw new Exception\RuntimeException('No host to connect to.', self::INVALID_HOST);
        }

        $context = $this->createStreamContext();

        //var_dump(file_get_contents('http://demo.mobiledetect.net/test/jsonrpc.php?page=test', false, $context));
        //print_r(stream_context_get_options($context));

        //$ipAddress = gethostbyname($this->getHost());
        // Create the handler. We use this in the request and response.
        $handler = $this->openStream(
            $this->getProtocol() . '://' . $this->getHost() . ':' . $this->getPort(),
            $errno,
            $errstr,
            30,
            $flags,
            $context
        );

        if ($handler === false) {
            $this->close();
            throw new Exception\RuntimeException(
                sprintf('Cannot open stream connection. [Reason: %s] [Code: %d]', $errstr, $errno),
                self::ERROR_OPENING_STREAM
            );
        }

        // Store the handler resource for later use.
        $this->setHandler($handler);

        return true;
    }

    protected function openStream($remote_socket, &$errno, &$errstr, $timeout, $flags)
    {
        $handler = \stream_socket_client($remote_socket, $errno, $errstr, $timeout, $flags);
        return $handler;
    }

    protected function createStreamContext()
    {
        // Create the stream context.
        $context = \stream_context_create();

        // Apply stream options.
        \stream_context_set_option($context, 'http', 'timeout', $this->getOption('timeout'));
        \stream_context_set_option($context, 'http', 'follow_location', $this->getOption('follow_location'));
        \stream_context_set_option($context, 'http', 'max_redirects', $this->getOption('max_redirects'));
        if ($this->getProxy()) {
            \stream_context_set_option($context, 'http', 'proxy', $this->getProtocol() . '://' . $this->getProxy());
            \stream_context_set_option($context, 'http', 'request_fulluri', true);
        }

        return $context;
    }

    public function send(AbstractMessage $request = null)
    {
        if (!$this->getHandler()) {
            throw new Exception\RuntimeException('Trying to write but no connection is available.', self::INVALID_HANDLER);
        }

        // Apply important stream/request options.
        $this->setStreamTimeout($this->getOption('request_timeout'));
        $this->setStreamBlockingMode($this->getOption('request_blocking_mode'));

        // Build the request object.
        if (is_null($request)) {
           $request = $this->request();
        }

        // Apply mandatory headers.
        $request->addHeader('Host', $this->getHost());
        $request->addHeader('Content-length', strlen($request->getBody()));
        $request->addHeader('Accept', '*/*');
        if ($this->getOption('persistent')) {
            $request->addHeader('Connection', 'keep-alive');
        } else {
            $request->addHeader('Connection', 'close');
        }

        $send = $this->writeToStream();

        //print_r(stream_get_meta_data($this->handler));
        //var_dump($send);

        if ($send === false) {
            throw new Exception\RuntimeException('Could not write the request.', self::ERROR_WRITING_TO_STREAM);
        }

        return true;
    }

    protected function setStreamTimeout($timeout)
    {
        return \stream_set_timeout($this->getHandler(), (float)$timeout);
    }

    protected function setStreamBlockingMode($blockingMode)
    {
        return \stream_set_blocking($this->getHandler(), (bool)$blockingMode);
    }

    protected function writeToStream()
    {
        $send = fwrite($this->getHandler(), $this->request()->__toString());
        return $send;
    }

    public function read()
    {
        $headers = '';
        $headersArray = array();
        $gotResponseHeaders = false;
        $response = '';

        // @todo Consider code refactoring using http://stackoverflow.com/questions/18349123/stream-set-timeout-doesnt-work-in-php
        while (($line = $this->readStreamLine($this->getHandler())) !== false) {
            var_dump($line);
            // print_r(stream_get_meta_data($this->handler));
            // Read the headers of the current response.
            if (!$gotResponseHeaders) {
                $headers .= $line;
                if (rtrim($line) === '') {
                    $headersArray = $this->getRequest()->convertHeadersToArray($headers);
                    $gotResponseHeaders = true;
                    //echo "\n". '---Begin response HTTP headers---' . "\n";
                    // var_dump($headers);
                    //var_dump($this->request()->getPath());
                    //echo "---End response HTTP headers---\n\n";
                }
            } else {
                $currentPosition = $this->getStreamPosition($this->getHandler());
                echo "\n\n";
                echo $currentPosition;
                echo "\n\n";
                $bodyLength = isset($headersArray['Content-length']) ? (int)$headersArray['Content-length'] : 0;

                $response .= $line;

                if ($bodyLength > 0) {
                    $maxReadLength = $bodyLength + $currentPosition;
                    if ($currentPosition > $maxReadLength) {
                        break;
                    }
                } else {
                    if (feof($this->getHandler())) {
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

        //echo "\n" . '---Begin Response---' . "\n";
        //var_dump($response);
        //echo "---End response---\n\n\n\n";

        return $response;
    }

    public function readStreamLine($handler)
    {
        return \fgets($handler);
    }

    protected function getStreamPosition($handler)
    {
        return \ftell($handler);
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