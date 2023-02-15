<?php

/**
 * Thanks to https://github.com/flaushi for his suggestion:
 * https://github.com/doctrine/dbal/issues/2873#issuecomment-534956358
 */
namespace DeliciousBrains\WP_Offload_SES\Carbon\Doctrine;

use DeliciousBrains\WP_Offload_SES\Carbon\Carbon;
use DeliciousBrains\WP_Offload_SES\Doctrine\DBAL\Types\VarDateTimeType;
class DateTimeType extends VarDateTimeType implements CarbonDoctrineType
{
    /** @use CarbonTypeConverter<Carbon> */
    use CarbonTypeConverter;
}
