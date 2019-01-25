<?php

namespace Dingo\Api\Console\Command;

use Illuminate\Console\Command;
use Illuminate\Filesystem\Filesystem;
use Illuminate\Contracts\Console\Kernel;

class Cache extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    public $signature = 'api:cache';

    /**
     * The console command description.
     *
     * @var string
     */
    public $description = 'Create a route cache file for faster route registration';

    /**
     * Execute the console command.
     *
     * @param \Illuminate\Filesystem\Filesystem $files
     *
     * @return mixed
     */
    public function handle(Filesystem $files)
    {
        $this->callSilent('route:clear');

        $app = $this->getFreshApplication();

        $this->call('route:cache');

        $routes = $app['api.router']->getAdapterRoutes();

        foreach ($routes as $collection) {
            foreach ($collection as $route) {
                $app['api.router.adapter']->prepareRouteForSerialization($route);
            }
        }

        $stub = "app('api.router')->setAdapterRoutes(unserialize(base64_decode('{{routes}}')));";
        $path = $this->laravel->getCachedRoutesPath();

        if (! $files->exists($path)) {
            $stub = "<?php\n\n$stub";
        }

        $files->append(
            $path,
            str_replace('{{routes}}', base64_encode(serialize($routes)), $stub)
        );
    }

    /**
     * Get a fresh application instance.
     *
     * @return \Illuminate\Contracts\Container\Container
     */
    protected function getFreshApplication()
    {
        if (method_exists($this->laravel, 'bootstrapPath')) {
            $app = require $this->laravel->bootstrapPath().'/app.php';
        } else {
            $app = require $this->laravel->basePath().'/bootstrap/app.php';
        }

        $app->make(Kernel::class)->bootstrap();

        return $app;
    }
}
