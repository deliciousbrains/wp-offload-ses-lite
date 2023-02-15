<?php

namespace DeliciousBrains\WP_Offload_SES\Aws3\Aws\Arn;

/**
 * This class represents an S3 Object bucket ARN, which is in the
 * following format:
 *
 * @internal
 */
class ObjectLambdaAccessPointArn extends AccessPointArn
{
    /**
     * Parses a string into an associative array of components that represent
     * a ObjectLambdaAccessPointArn
     *
     * @param $string
     * @return array
     */
    public static function parse($string)
    {
        $data = parent::parse($string);
        return parent::parseResourceTypeAndId($data);
    }
    /**
     *
     * @param array $data
     */
    protected static function validate(array $data)
    {
        parent::validate($data);
        self::validateRegion($data, 'S3 Object Lambda ARN');
        self::validateAccountId($data, 'S3 Object Lambda ARN');
    }
}
