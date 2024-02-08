<?php

namespace DeliciousBrains\WP_Offload_SES\WP_Queue\Connections;

use Exception;
use DeliciousBrains\WP_Offload_SES\WP_Queue\Job;
/**
 * A minimal implementation of the ConnectionInterface that handles a pushed
 * Job immediately.
 *
 * Useful for local development when only wanting to debug how Job items are handled.
 */
class SyncConnection implements ConnectionInterface
{
    /**
     * Execute the job immediately without pushing to the queue.
     *
     * @param Job $job
     * @param int $delay
     *
     * @return bool|int
     */
    public function push(Job $job, $delay = 0)
    {
        $job->handle();
        return \true;
    }
    /**
     * Retrieve a job from the queue.
     *
     * @return bool|Job
     */
    public function pop()
    {
        return \false;
    }
    /**
     * Delete a job from the queue.
     *
     * @param Job $job
     *
     * @return bool
     */
    public function delete($job)
    {
        return \false;
    }
    /**
     * Release a job back onto the queue.
     *
     * @param Job $job
     *
     * @return bool
     */
    public function release(Job $job)
    {
        return \false;
    }
    /**
     * Push a job onto the failure queue.
     *
     * @param Job       $job
     * @param Exception $exception
     *
     * @return bool
     */
    public function failure($job, Exception $exception)
    {
        return \false;
    }
    /**
     * Get total jobs in the queue.
     *
     * @return int
     */
    public function jobs()
    {
        return 0;
    }
    /**
     * Get total jobs in the failures queue.
     *
     * @return int
     */
    public function failed_jobs()
    {
        return 0;
    }
}
