<?php
namespace HttpClient\Message;

class Response extends AbstractMessage implements MessageInterface
{
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
        return ($this->getHeader('Transfer-encoding') == 'chunked');
    }

    public function getBodyLength()
    {
        return $this->getHeader('Content-length');
    }
}