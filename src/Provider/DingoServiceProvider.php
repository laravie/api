<?php

namespace Dingo\Api\Provider;

use RuntimeException;
use Dingo\Api\Auth\Auth;
use Dingo\Api\Http\Request;
use Dingo\Api\Http\Response;
use Dingo\Api\Console\Command;
use Dingo\Api\Exception\Handler as ExceptionHandler;
use Dingo\Api\Transformer\Factory as TransformerFactory;

class DingoServiceProvider extends ServiceProvider
{
    /**
     * Boot the service provider.
     *
     * @return void
     */
    public function boot()
    {
        $this->setResponseStaticInstances();

        Request::setAcceptParser($this->app['Dingo\Api\Http\Parser\Accept']);

        $this->app->rebinding('api.routes', function ($app, $routes) {
            $app['api.url']->setRouteCollections($routes);
        });
    }

    protected function setResponseStaticInstances()
    {
        Response::setFormatters($this->config('formats'));
        Response::setFormatsOptions($this->config('formatsOptions'));
        Response::setTransformer($this->app['api.transformer']);
        Response::setEventDispatcher($this->app['events']);
    }

    /**
     * Register the service provider.
     *
     * @return void
     */
    public function register()
    {
        $this->registerConfig();

        $this->registerClassAliases();

        $this->app->register(RoutingServiceProvider::class);

        $this->app->register(HttpServiceProvider::class);

        $this->registerExceptionHandler();

        $this->registerAuth();

        $this->registerTransformer();

        $this->registerDocsCommand();
    }

    /**
     * Register the configuration.
     *
     * @return void
     */
    protected function registerConfig()
    {
        $this->mergeConfigFrom(\realpath(__DIR__.'/../../config/api.php'), 'api');

        if (! $this->app->runningInConsole() && empty($this->config('prefix')) && empty($this->config('domain'))) {
            throw new RuntimeException('Unable to boot ApiServiceProvider, configure an API domain or prefix.');
        }
    }

    /**
     * Register the class aliases.
     *
     * @return void
     */
    protected function registerClassAliases()
    {
        $serviceAliases = [
            Request::class => 'Dingo\Api\Contract\Http\Request',
            'api.http.validator' => 'Dingo\Api\Http\RequestValidator',
            'api.http.response' => 'Dingo\Api\Http\Response\Factory',
            'api.router' => 'Dingo\Api\Routing\Router',
            'api.router.adapter' => 'Dingo\Api\Contract\Routing\Adapter',
            'api.auth' => Auth::class,
            'api.limiting' => 'Dingo\Api\Http\RateLimit\Handler',
            'api.transformer' => TransformerFactory::class,
            'api.url' => 'Dingo\Api\Routing\UrlGenerator',
            'api.exception' => [ExceptionHandler::class, 'Dingo\Api\Contract\Debug\ExceptionHandler'],
        ];

        foreach ($serviceAliases as $key => $aliases) {
            foreach ((array) $aliases as $alias) {
                $this->app->alias($key, $alias);
            }
        }
    }

    /**
     * Register the exception handler.
     *
     * @return void
     */
    protected function registerExceptionHandler()
    {
        $this->app->singleton('api.exception', function ($app) {
            return new ExceptionHandler(
                $app['Illuminate\Contracts\Debug\ExceptionHandler'], $this->config('errorFormat'), $this->config('debug')
            );
        });
    }

    /**
     * Register the auth.
     *
     * @return void
     */
    protected function registerAuth()
    {
        $this->app->singleton('api.auth', function ($app) {
            return new Auth($app['Dingo\Api\Routing\Router'], $app, $this->config('auth'));
        });
    }

    /**
     * Register the transformer factory.
     *
     * @return void
     */
    protected function registerTransformer()
    {
        $this->app->singleton('api.transformer', function ($app) {
            return new TransformerFactory($app, $this->config('transformer'));
        });
    }

    /**
     * Register the documentation command.
     *
     * @return void
     */
    protected function registerDocsCommand()
    {
        $this->app->singleton(Command\Docs::class, function ($app) {
            return new Command\Docs(
                $app['Dingo\Api\Routing\Router'],
                $app['Dingo\Blueprint\Blueprint'],
                $app['Dingo\Blueprint\Writer'],
                $this->config('name'),
                $this->config('version')
            );
        });

        $this->commands([Command\Docs::class]);
    }
}
