# Changelog for 2.1

This changelog references the relevant changes (bug and security fixes) done to `laravie/api`.

## 2.1.4

Released: 2019-03-20

### Changes

* Use instance of `Carbon\Carbon` when setting cache for Laravel 5.8 TTL breaking change support.

## 2.1.3

Released: 2019-03-15

### Changes

* Implement Route pattern match checking for params in URL.
* Replace `str_*` helper with `Illuminate\Support\Str`.

### Fixes

* Fixes unit tests by adding router adapter instance to app container.

## 2.1.2

Released: 2019-02-19

### Fixes

* Fixes a problem whereby parameters passed through internal requests are not available in Laravel's `$request->input()`.
* Fixes support for custom data includes in `Dingo\Api\Transformer\Adapter\Fractal`.
* Fixed issue on URLGenerator for signed routes.

### Changes

* Convert `Illuminate\Database\Eloquent\ModelNotFoundException` to `Symfony\Component\HttpKernel\Exception\NotFoundHttpException` instead of just tweaking the response status code.
* Use `Illuminate\Foundation\Application::bootstrapPath()` by default when caching routes.

## 2.1.1

Released: 2018-12-30

### Fixes

* Fixed `sha1() expects parameter 1 to be string, null given` when setting etag on returning empty content.

## 2.1.0

Released: 2018-12-25

### Changes

* Mark `league/fractal` dependency as optional.
* Convert `Illuminate\Database\Eloquent\ModelNotFoundException` to `404`.
* Use `Arr` and `Str` instead of function helpers.
