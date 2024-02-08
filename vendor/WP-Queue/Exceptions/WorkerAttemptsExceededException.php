<?php

namespace DeliciousBrains\WP_Offload_SES\WP_Queue\Exceptions;

use Exception;
/**
 * Exception for when maximum number of attempts to process a job exceeded.
 */
class WorkerAttemptsExceededException extends Exception
{
}
