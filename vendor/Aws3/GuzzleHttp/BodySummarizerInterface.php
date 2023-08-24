<?php

namespace DeliciousBrains\WP_Offload_SES\Aws3\GuzzleHttp;

use DeliciousBrains\WP_Offload_SES\Aws3\Psr\Http\Message\MessageInterface;
interface BodySummarizerInterface
{
    /**
     * Returns a summarized message body.
     */
    public function summarize(MessageInterface $message) : ?string;
}
