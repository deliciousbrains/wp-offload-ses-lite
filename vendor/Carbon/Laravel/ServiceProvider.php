<?php

/**
 * This file is part of the Carbon package.
 *
 * (c) Brian Nesbitt <brian@nesbot.com>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */
namespace DeliciousBrains\WP_Offload_SES\Carbon\Laravel;

use DeliciousBrains\WP_Offload_SES\Carbon\Carbon;
use DeliciousBrains\WP_Offload_SES\Carbon\CarbonImmutable;
use DeliciousBrains\WP_Offload_SES\Carbon\CarbonInterval;
use DeliciousBrains\WP_Offload_SES\Carbon\CarbonPeriod;
use DeliciousBrains\WP_Offload_SES\Illuminate\Contracts\Events\Dispatcher as DispatcherContract;
use DeliciousBrains\WP_Offload_SES\Illuminate\Events\Dispatcher;
use DeliciousBrains\WP_Offload_SES\Illuminate\Events\EventDispatcher;
use DeliciousBrains\WP_Offload_SES\Illuminate\Support\Carbon as IlluminateCarbon;
use DeliciousBrains\WP_Offload_SES\Illuminate\Support\Facades\Date;
use Throwable;
class ServiceProvider extends \DeliciousBrains\WP_Offload_SES\Illuminate\Support\ServiceProvider
{
    /** @var callable|null */
    protected $appGetter = null;
    /** @var callable|null */
    protected $localeGetter = null;
    public function setAppGetter(?callable $appGetter) : void
    {
        $this->appGetter = $appGetter;
    }
    public function setLocaleGetter(?callable $localeGetter) : void
    {
        $this->localeGetter = $localeGetter;
    }
    public function boot()
    {
        $this->updateLocale();
        if (!$this->app->bound('events')) {
            return;
        }
        $service = $this;
        $events = $this->app['events'];
        if ($this->isEventDispatcher($events)) {
            $events->listen(\class_exists('DeliciousBrains\\WP_Offload_SES\\Illuminate\\Foundation\\Events\\LocaleUpdated') ? 'Illuminate\\Foundation\\Events\\LocaleUpdated' : 'locale.changed', function () use($service) {
                $service->updateLocale();
            });
        }
    }
    public function updateLocale()
    {
        $locale = $this->getLocale();
        if ($locale === null) {
            return;
        }
        Carbon::setLocale($locale);
        CarbonImmutable::setLocale($locale);
        CarbonPeriod::setLocale($locale);
        CarbonInterval::setLocale($locale);
        if (\class_exists(IlluminateCarbon::class)) {
            IlluminateCarbon::setLocale($locale);
        }
        if (\class_exists(Date::class)) {
            try {
                $root = Date::getFacadeRoot();
                $root->setLocale($locale);
            } catch (Throwable $e) {
                // Non Carbon class in use in Date facade
            }
        }
    }
    public function register()
    {
        // Needed for Laravel < 5.3 compatibility
    }
    protected function getLocale()
    {
        if ($this->localeGetter) {
            return ($this->localeGetter)();
        }
        $app = $this->getApp();
        $app = $app && \method_exists($app, 'getLocale') ? $app : $this->getGlobalApp('translator');
        return $app ? $app->getLocale() : null;
    }
    protected function getApp()
    {
        if ($this->appGetter) {
            return ($this->appGetter)();
        }
        return $this->app ?? $this->getGlobalApp();
    }
    protected function getGlobalApp(...$args)
    {
        return \function_exists('DeliciousBrains\\WP_Offload_SES\\app') ? \DeliciousBrains\WP_Offload_SES\app(...$args) : null;
    }
    protected function isEventDispatcher($instance)
    {
        return $instance instanceof EventDispatcher || $instance instanceof Dispatcher || $instance instanceof DispatcherContract;
    }
}
