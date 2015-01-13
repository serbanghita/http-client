<?php
namespace HttpClient\Transport;

use HttpClient\Message\AbstractMessage;
use HttpClient\Message\Headers;
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
        'request_timeout' => 10,
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
            throw new Exception\RuntimeException(
                'Trying to write but no connection is available.',
                self::INVALID_HANDLER
            );
        }

        // Apply important stream/request options.
        $this->setStreamTimeout($this->getOption('request_timeout'));
        $this->setStreamBlockingMode($this->getOption('request_blocking_mode'));

        // Build the request object.
        if (is_null($request)) {
            $request = $this->request();
        }

        // Apply mandatory headers.
        $request->setHeaders(new Headers());
        $request->headers()->add('Host', $this->getHost());
        $request->headers()->add('Content-length', strlen($request->getBody()));
        $request->headers()->add('Accept', '*/*');
        if ($this->getOption('persistent')) {
            $request->headers()->add('Connection', 'keep-alive');
        } else {
            $request->headers()->add('Connection', 'close');
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
        $send = \fwrite($this->getHandler(), $this->request()->__toString());
        return $send;
    }

    /**
     * Read the remote response and return the
     * response body.
     *
     * @return string
     */
    public function read()
    {
        // Defaults.
        $gotResponseHeaders = false;
        $responseHeaders = '';
        $responseBody = '';

        // Create a new response object for
        // the incoming data.
        $this->createResponse();

        // Reading the incoming stream.
        while (($line = $this->readStreamLine()) !== false) {
            // print_r($this->getStreamMetaData($this->handler));

            // Read the headers of the current response.
            if (!$this->getResponse()->headersWereParsed()) {
                // Store the response headers for later.
                $responseHeaders .= $line;

                // Check for headers boundary.
                if ($this->isStreamHeadersEnd($line)) {
                    // Save the response headers.
                    $this->getResponse()->setHeaders(new Headers($responseHeaders));
                    $this->getResponse()->setHeadersWereParsed(true);
                }
            } else {
                // Dealing with chunked transfer message.
                // @see http://en.wikipedia.org/wiki/Chunked_transfer_encoding
                if ($this->getResponse()->isChunked()) {
                    if (!isset($chunkLength)) {
                        $chunkLength = hexdec($line);
                        $chunkLengthReadSoFar = 0;
                        continue;
                    }

                    if ($chunkLength > 0) {
                        // Store the response body for later.
                        $this->getResponse()->addBodyChunk($line);
                        $chunkLengthReadSoFar += strlen($line);
                        if ($chunkLengthReadSoFar >= $chunkLength) {
                            unset($chunkLength);
                        }
                        continue;
                    }

                    if ($chunkLength == 0) {
                        break;
                    }
                }

                $currentPosition = $this->getStreamPosition();

                // Store te response body for later.
                $this->getResponse()->addBodyChunk($line);


                if ($this->getResponse()->getBodyLength() > 0) {
                    // If we know the body length, check if we reached the maximum length.
                    $maxReadLength = $this->getResponse()->getBodyLength() + $currentPosition;
                    if ($currentPosition > $maxReadLength) {
                        break;
                    }
                } else {
                    // If body length is ambiguous, check for EOF or other
                    // similar signals.
                    if ($this->getStreamEOF()) {
                        break;
                    }
                }
            }
        }

        if ($this->getResponse()->isClosed()) {
            $this->close();
        }

        return $this->getResponse()->getBody();
    }

    protected function readStreamLine()
    {
        return \fgets($this->getHandler());
    }

    protected function isStreamHeadersEnd($line)
    {
        return (rtrim($line) === '');
    }

    protected function getStreamPosition()
    {
        return \ftell($this->getHandler());
    }

    protected function getStreamMetaData()
    {
        return \stream_get_meta_data($this->getHandler());
    }

    /**
     * Attempt to get EOF of the stream.
     * This help knowing when to close the request
     * or to send another in case of multiple non-async requests.
     *
     * @todo Study http://stackoverflow.com/questions/18349123/stream-set-timeout-doesnt-work-in-php
     * @return bool
     */
    protected function getStreamEOF()
    {
        $metaData = $this->getStreamMetaData();
        return \feof($this->getHandler()) ||
                ($metaData['unread_bytes']==0 && $metaData['eof']) ||
                $metaData['timed_out'];
    }

    /**
     * Permanently close the stream socket.
     *
     * @return bool
     */
    protected function shutDownStream()
    {
        \stream_socket_shutdown($this->getHandler(), STREAM_SHUT_RDWR);
        \fclose($this->getHandler());
        return true;
    }

    /**
     * Close the connection.
     *
     * @see http://chat.stackoverflow.com/transcript/message/7727858#7727858
     * @return bool
     */
    public function close()
    {
        if ($this->streamHandlerIsValid()) {
            $this->setStreamBlockingMode(false);
            $this->shutDownStream();
            return true;
        } else {
            return false;
        }
    }

    /**
     * Check if the stream handler is still valid.
     * Note: \get_resource_type should be 'stream' or 'persistent stream'.
     *
     * @return bool
     */
    protected function streamHandlerIsValid()
    {
        return \is_resource($this->getHandler()) && \get_resource_type($this->getHandler());
    }

}