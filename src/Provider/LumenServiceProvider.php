<?php

namespace Dingo\Api\Provider;

use ReflectionClass;
use Laravel\Lumen\Application;
use Dingo\Api\Http\FormRequest;
use Laravel\Lumen\Http\Redirector;
use Dingo\Api\Http\Middleware\Auth;
use Dingo\Api\Http\Middleware\Request;
use Dingo\Api\Http\Middleware\RateLimit;
use FastRoute\Dispatcher\GroupCountBased;
use Dingo\Api\Http\Middleware\PrepareController;
use Dingo\Api\Lumen\Decorator\RequestMiddleware;
use FastRoute\RouteParser\Std as StdRouteParser;
use Illuminate\Http\Request as IlluminateRequest;
use Dingo\Api\Routing\Adapter\Lumen as LumenAdapter;
use Illuminate\Contracts\Validation\ValidatesWhenResolved;
use FastRoute\DataGenerator\GroupCountBased as GcbDataGenerator;

class LumenServiceProvider extends DingoServiceProvider
{
    /**
     * Boot the service provider.
     *
     * @throws \ReflectionException
     *
     * @return void
     */
    public function boot()
    {
        parent::boot();

        $decorator = new RequestMiddleware($this->app);
        $decorator->addRequestMiddlewareToBeginning();

        // Because Lumen sets the route resolver at a very weird point we're going to
        // have to use reflection whenever the request instance is rebound to
        // set the route resolver to get the current route.
        $this->app->rebinding(IlluminateRequest::class, static function ($app, $request) {
            $request->setRouteResolver(static function () use ($app) {
                $reflection = new ReflectionClass($app);

                $property = $reflection->getProperty('currentRoute');
                $property->setAccessible(true);

                return $property->getValue($app);
            });

            $request->setUserResolver(static function () use ($app) {
                $app->make('api.auth')->user();
            });
        });

        $this->app->afterResolving(ValidatesWhenResolved::class, static function ($resolved) {
            $resolved->validateResolved();
        });

        $this->app->resolving(FormRequest::class, function (FormRequest $request, Application $app) {
            $this->initializeRequest($request, $app['request']);

            $request->setContainer($app)->setRedirector($app->make(Redirector::class));
        });

        $this->app->routeMiddleware([
            'api.auth' => Auth::class,
            'api.throttle' => RateLimit::class,
            'api.controllers' => PrepareController::class,
        ]);
    }

    /**
     * Register the service provider.
     *
     * @return void
     */
    public function register()
    {
        parent::register();

        $this->app->singleton('api.router.adapter', function ($app) {
            return new LumenAdapter($app, new StdRouteParser, new GcbDataGenerator, $this->getDispatcherResolver());
        });
    }

    /**
     * Get the dispatcher resolver callback.
     *
     * @return \Closure
     */
    protected function getDispatcherResolver()
    {
        return function ($routeCollector) {
            return new GroupCountBased($routeCollector->getData());
        };
    }

    /**
     * Initialize the form request with data from the given request.
     *
     * @param FormRequest       $form
     * @param IlluminateRequest $current
     *
     * @return void
     */
    protected function initializeRequest(FormRequest $form, IlluminateRequest $current)
    {
        $files = $current->files->all();

        $files = \is_array($files) ? \array_filter($files) : $files;

        $form->initialize(
            $current->query->all(),
            $current->request->all(),
            $current->attributes->all(),
            $current->cookies->all(),
            $files,
            $current->server->all(),
            $current->getContent()
        );

        $form->setJson($current->json());

        if ($session = $current->getSession()) {
            $form->setLaravelSession($session);
        }

        $form->setUserResolver($current->getUserResolver());

        $form->setRouteResolver($current->getRouteResolver());
    }

    /**
     * Load API configuration.
     *
     * @return void
     */
    protected function loadApiConfiguration()
    {
        $this->mergeConfigFrom(realpath(__DIR__.'/../../config/api.php'), 'api');

        $this->app->configure('api');
    }
}
