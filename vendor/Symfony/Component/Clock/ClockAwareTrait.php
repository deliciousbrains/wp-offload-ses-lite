<?php

/*
 * This file is part of the Symfony package.
 *
 * (c) Fabien Potencier <fabien@symfony.com>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */
namespace DeliciousBrains\WP_Offload_SES\Symfony\Component\Clock;

use DeliciousBrains\WP_Offload_SES\Psr\Clock\ClockInterface;
use DeliciousBrains\WP_Offload_SES\Symfony\Contracts\Service\Attribute\Required;
/**
 * A trait to help write time-sensitive classes.
 *
 * @author Nicolas Grekas <p@tchwork.com>
 */
trait ClockAwareTrait
{
    private readonly ClockInterface $clock;
    #[\Symfony\Contracts\Service\Attribute\Required]
    public function setClock(ClockInterface $clock) : void
    {
        $this->clock = $clock;
    }
    /**
     * @return DatePoint
     */
    protected function now() : \DateTimeImmutable
    {
        $now = ($this->clock ??= new Clock())->now();
        return $now instanceof DatePoint ? $now : DatePoint::createFromInterface($now);
    }
}
