<?php

namespace DeliciousBrains\WP_Offload_SES\WP_Queue;

use DeliciousBrains\WP_Offload_SES\WP_Queue\Connections\DatabaseConnection;
use DeliciousBrains\WP_Offload_SES\WP_Queue\Connections\RedisConnection;
use DeliciousBrains\WP_Offload_SES\WP_Queue\Connections\SyncConnection;
use DeliciousBrains\WP_Offload_SES\WP_Queue\Exceptions\ConnectionNotFoundException;
class QueueManager
{
    /**
     * @var array
     */
    protected static $instances = array();
    /**
     * Resolve a Queue instance for required connection.
     *
     * @param string $connection
     *
     * @return Queue
     */
    public static function resolve($connection)
    {
        if (isset(static::$instances[$connection])) {
            return static::$instances[$connection];
        }
        return static::build($connection);
    }
    /**
     * Build a queue instance.
     *
     * @param string $connection
     *
     * @return Queue
     * @throws \Exception
     */
    protected static function build($connection)
    {
        $connections = static::connections();
        if (empty($connections[$connection])) {
            throw new \DeliciousBrains\WP_Offload_SES\WP_Queue\Exceptions\ConnectionNotFoundException();
        }
        static::$instances[$connection] = new \DeliciousBrains\WP_Offload_SES\WP_Queue\Queue($connections[$connection]);
        return static::$instances[$connection];
    }
    /**
     * Get available connections.
     *
     * @return array
     */
    protected static function connections()
    {
        $connections = array('database' => new \DeliciousBrains\WP_Offload_SES\WP_Queue\Connections\DatabaseConnection($GLOBALS['wpdb']), 'redis' => new \DeliciousBrains\WP_Offload_SES\WP_Queue\Connections\RedisConnection(), 'sync' => new \DeliciousBrains\WP_Offload_SES\WP_Queue\Connections\SyncConnection());
        return apply_filters('wp_queue_connections', $connections);
    }
}
