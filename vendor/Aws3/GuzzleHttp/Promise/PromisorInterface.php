<?php

declare (strict_types=1);
namespace DeliciousBrains\WP_Offload_SES\Aws3\GuzzleHttp\Promise;

/**
 * Interface used with classes that return a promise.
 */
interface PromisorInterface
{
    /**
     * Returns a promise.
     */
    public function promise() : PromiseInterface;
}
