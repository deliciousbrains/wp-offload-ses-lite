<?php

/**
 * Thanks to https://github.com/flaushi for his suggestion:
 * https://github.com/doctrine/dbal/issues/2873#issuecomment-534956358
 */
namespace DeliciousBrains\WP_Offload_SES\Carbon\Doctrine;

use DeliciousBrains\WP_Offload_SES\Carbon\CarbonImmutable;
use DeliciousBrains\WP_Offload_SES\Doctrine\DBAL\Types\VarDateTimeImmutableType;
class DateTimeImmutableType extends VarDateTimeImmutableType implements CarbonDoctrineType
{
    /** @use CarbonTypeConverter<CarbonImmutable> */
    use CarbonTypeConverter;
    /**
     * @return class-string<CarbonImmutable>
     */
    protected function getCarbonClassName() : string
    {
        return CarbonImmutable::class;
    }
}
