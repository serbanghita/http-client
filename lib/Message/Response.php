<?php
namespace HttpClient\Message;

class Response extends AbstractMessage implements MessageInterface
{
    /**
     * @var Headers
     */
    protected $headers;
    protected $headersWereParsed = false;

    public function setHeadersWereParsed($headersWereParsed)
    {
        $this->headersWereParsed = (bool)$headersWereParsed;
    }

    public function headersWereParsed()
    {
        return $this->headersWereParsed;
    }

    public function isChunked()
    {
        return ($this->headers()->get('Transfer-encoding') == 'chunked');
    }

    /**
     * Check for the 'Connection: close' header.
     * @return bool
     */
    public function isClosed()
    {
        return ($this->headers()->get('Connection') == 'close');
    }

    public function getBodyLength()
    {
        return $this->headers()->get('Content-length');
    }
}