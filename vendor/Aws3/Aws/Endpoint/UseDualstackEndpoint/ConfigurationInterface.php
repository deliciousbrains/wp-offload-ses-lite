<?php

namespace DeliciousBrains\WP_Offload_SES\Aws3\Aws\Endpoint\UseDualstackEndpoint;

interface ConfigurationInterface
{
    /**
     * Returns whether or not to use a DUALSTACK endpoint
     *
     * @return bool
     */
    public function isUseDualstackEndpoint();
    /**
     * Returns the configuration as an associative array
     *
     * @return array
     */
    public function toArray();
}
