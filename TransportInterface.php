<?php
namespace GenericApiClient\Transport;

use GenericApiClient\Transport\Request\Request as Request;

interface TransportInterface
{
    /**
     * @param Options $options
     * @return mixed
     */
    public function connect(Options $options);

    /**
     * @param Request $request
     * @return mixed
     */
    public function send(Request $request);

    /**
     * @return mixed
     */
    public function close();
}