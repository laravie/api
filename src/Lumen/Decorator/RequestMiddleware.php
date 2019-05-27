<?php

namespace Dingo\Api\Lumen\Decorator;

use ReflectionClass;
use Laravel\Lumen\Application;
use Dingo\Api\Http\Middleware\Request;

class RequestMiddleware
{
    /**
     * Lumen Application.
     *
     * @var \Laravel\Lumen\Application
     */
    protected $app;

    /**
     * Construct Request Middleware decorator.
     *
     * @param \Laravel\Lumen\Application $app
     */
    public function __construct(Application $app)
    {
        $this->app = $app;
    }

    /**
     * Add the request middleware to the beginning of the middleware stack on the
     * Lumen application instance.
     *
     * @return void
     */
    public function addRequestMiddlewareToBeginning(): void
    {
        if (\method_exists($this->app, 'addRequestMiddlewareToBeginning')) {
            $this->app->addRequestMiddlewareToBeginning();

            return;
        }

        $reflection = new ReflectionClass($this->app);

        $this->app[Request::class]->mergeMiddlewares(
            $middleware = $this->gatherLumenMiddlewares($reflection)
        );

        \array_unshift($middleware, Request::class);

        $property = $reflection->getProperty('middleware');
        $property->setAccessible(true);
        $property->setValue($this->app, $middleware);
        $property->setAccessible(false);
    }

    /**
     * Remove the global application middleware as it's run from this packages
     * Request middleware. Lumen runs middleware later in its life cycle
     * which results in some middleware being executed twice.
     *
     * @return void
     */
    public function removeGlobalMiddlewareFromLumen(): void
    {
        if (\method_exists($this->app, 'removeGlobalMiddlewareFromLumen')) {
            $this->app->removeGlobalMiddlewareFromLumen();

            return;
        }

        $reflection = new ReflectionClass($this->app);
        $oldMiddlewares = $this->gatherLumenMiddlewares($reflection);
        $newMiddlewares = [];

        foreach ($oldMiddlewares as $middle) {
            if (\method_exists($middle, 'terminate') && $middle != Request::class) {
                $newMiddlewares[] = $middle;
            }
        }

        $property = $reflection->getProperty('middleware');
        $property->setValue($this->app, $newMiddlewares);
        $property->setAccessible(false);
    }

    /**
     * Gather the application middleware besides this one so that we can send
     * our request through them, exactly how the developer wanted.
     *
     * @param \ReflectionClass $reflection
     *
     * @return array
     */
    protected function gatherLumenMiddlewares(ReflectionClass $reflection): array
    {
        $property = $reflection->getProperty('middleware');
        $property->setAccessible(true);

        return $property->getValue($this->app);
    }
}
