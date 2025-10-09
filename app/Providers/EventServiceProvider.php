<?php

namespace App\Providers;

use Illuminate\Foundation\Support\Providers\EventServiceProvider as ServiceProvider;

class EventServiceProvider extends ServiceProvider
{
    /**
     * The event listener mappings for the application.
     *
     * @var array<class-string, array<int, class-string>>
     */
    protected $listen = [
        // You can leave this empty when using auto-discovery
    ];

    /**
     * Determine if events and listeners should be automatically discovered.
     */
    public function shouldDiscoverEvents(): bool
    {
        return true;
    }

    /**
     * Get the paths that should be scanned for events.
     */
    protected function discoverEventsWithin(): array
    {
        return [
            $this->app->basePath('app/Listeners'),
            $this->app->basePath('app/Events'),
        ];
    }
}
