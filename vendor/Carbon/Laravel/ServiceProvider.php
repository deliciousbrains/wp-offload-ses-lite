<?php

namespace DeliciousBrains\WP_Offload_SES\Carbon\Laravel;

use DeliciousBrains\WP_Offload_SES\Carbon\Carbon;
use DeliciousBrains\WP_Offload_SES\Illuminate\Events\Dispatcher;
use DeliciousBrains\WP_Offload_SES\Illuminate\Events\EventDispatcher;
use DeliciousBrains\WP_Offload_SES\Illuminate\Translation\Translator as IlluminateTranslator;
use DeliciousBrains\WP_Offload_SES\Symfony\Component\Translation\Translator;
class ServiceProvider extends \DeliciousBrains\WP_Offload_SES\Illuminate\Support\ServiceProvider
{
    public function boot()
    {
        $service = $this;
        $events = $this->app['events'];
        if ($events instanceof EventDispatcher || $events instanceof Dispatcher) {
            $events->listen(\class_exists('DeliciousBrains\\WP_Offload_SES\\Illuminate\\Foundation\\Events\\LocaleUpdated') ? 'Illuminate\\Foundation\\Events\\LocaleUpdated' : 'locale.changed', function () use($service) {
                $service->updateLocale();
            });
            $service->updateLocale();
        }
    }
    public function updateLocale()
    {
        $translator = $this->app['translator'];
        if ($translator instanceof Translator || $translator instanceof IlluminateTranslator) {
            Carbon::setLocale($translator->getLocale());
        }
    }
    public function register()
    {
        // Needed for Laravel < 5.3 compatibility
    }
}
