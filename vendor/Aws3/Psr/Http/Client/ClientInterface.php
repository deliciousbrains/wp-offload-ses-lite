<?php

namespace DeliciousBrains\WP_Offload_SES\Aws3\Psr\Http\Client;

use DeliciousBrains\WP_Offload_SES\Aws3\Psr\Http\Message\RequestInterface;
use DeliciousBrains\WP_Offload_SES\Aws3\Psr\Http\Message\ResponseInterface;
interface ClientInterface
{
    /**
     * Sends a PSR-7 request and returns a PSR-7 response.
     *
     * @param RequestInterface $request
     *
     * @return ResponseInterface
     *
     * @throws \Psr\Http\Client\ClientExceptionInterface If an error happens while processing the request.
     */
    public function sendRequest(RequestInterface $request) : ResponseInterface;
}
