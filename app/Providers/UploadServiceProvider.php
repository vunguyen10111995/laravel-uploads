<?php

namespace App\Providers;

use App\Components\Upload\UploadManager;
use Illuminate\Contracts\Container\BindingResolutionException;
use Illuminate\Support\Arr;
use Illuminate\Support\ServiceProvider;
use App\Components\Upload\Handlers\{Avatar};
use App\Listeners\Listener;
use App\Listeners\StoreAvatarForUser;
use Illuminate\Support\Str;
use App\Events\UploadProcessing;
use App\Events\UploadProcessed;

class UploadServiceProvider extends ServiceProvider
{
    protected $handlers = [
        Avatar::class => StoreAvatarForUser::class,
    ];
    /**
     * Register services.
     *
     * @return void
     */
    public function register()
    {
        $this->registerManager();
    }

    /**
     * Bootstrap services.
     *
     * @return void
     * @throws BindingResolutionException
     */
    public function boot()
    {
        $this->app->make('upload')->cycle(function ($event) {
            $this->callListeners($event);
        });
    }

    protected function registerManager()
    {
        $this->app->singleton('upload', function ($app) {
            return tap(new UploadManager($app), function ($manager) {
                $this->registerHandlers($manager);
            });
        });
    }

    protected function registerHandlers($manager)
    {
        foreach (array_keys($this->handlers) as $handler) {
            $this->app->singleton($handler, function ($app) use ($handler) {
                return new $handler($app);
            });
        }

        foreach ([
            'avatar',
        ] as $key) {
            $this->{'add'. Str::studly($key).'Handler'}($manager, $key);
        }
    }

    protected function addAvatarHandler($manager, $key)
    {
        // add customCreators with key avatar => Avatar class => magic
        $manager->extend($key, function () {
            return $this->app->make(Avatar::class);
        });
    }

    public function callListeners($event)
    {
        $listeners = Arr::get($this->handlers, $this->getHandlerClass($event));

        if (is_null($listeners)) {
            return;
        }
        if (is_string($listeners)) {
            $listeners = (array) $listeners;
        }
        foreach ($listeners as $listener) {
            if (!class_exists($listener)) {
                continue;
            }
            $listener = $this->makeListener($listener, $event);
            if (!$listener instanceof Listener) {
                continue;
            }
            $this->callListener($listener, $event);
        }
    }

    protected function getHandlerClass($event)
    {
        return get_class(Arr::get($event->context, 'handler'));
    }

    protected function makeListener($listener, $event)
    {
        return $this->app->makeWith($listener, ['event' => $event]);
    }

    protected function callListener($listener, $event)
    {
        foreach ([
                     UploadProcessing::class => 'preprocess',
                     UploadProcessed::class => 'postprocess',
                 ] as $class => $method) {
            if ($event instanceof $class && method_exists($listener, $method)) {
                $listener->$method();
            }
        }
    }
}
