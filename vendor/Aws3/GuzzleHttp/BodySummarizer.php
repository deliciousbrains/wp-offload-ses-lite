<?php

namespace DeliciousBrains\WP_Offload_SES\Aws3\GuzzleHttp;

use DeliciousBrains\WP_Offload_SES\Aws3\Psr\Http\Message\MessageInterface;
final class BodySummarizer implements BodySummarizerInterface
{
    /**
     * @var int|null
     */
    private $truncateAt;
    public function __construct(int $truncateAt = null)
    {
        $this->truncateAt = $truncateAt;
    }
    /**
     * Returns a summarized message body.
     */
    public function summarize(MessageInterface $message) : ?string
    {
        return $this->truncateAt === null ? \DeliciousBrains\WP_Offload_SES\Aws3\GuzzleHttp\Psr7\Message::bodySummary($message) : \DeliciousBrains\WP_Offload_SES\Aws3\GuzzleHttp\Psr7\Message::bodySummary($message, $this->truncateAt);
    }
}
