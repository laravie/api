<?php

namespace Dingo\Api\Lumen\Adapter;

use Dingo\Api\Http\Middleware\Request;

trait RequestMiddleware
{
    /**
     * Add the request middleware to the beginning of the middleware stack on the
     * Lumen application instance.
     *
     * @return void
     */
    public function addRequestMiddlewareToBeginning(): void
    {
        $this->make(Request::class)->mergeMiddlewares(
            $middleware = $this->middleware
        );

        \array_unshift($middleware, Request::class);

        $this->middleware = $middleware;
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
        $oldMiddlewares = $this->middleware;
        $newMiddlewares = [];

        foreach ($oldMiddlewares as $middle) {
            if (\method_exists($middle, 'terminate') && $middle != Request::class) {
                $newMiddlewares[] = $middle;
            }
        }

        $this->middleware = $newMiddlewares;
    }
}
