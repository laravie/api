# Changelog for 2.0

This changelog references the relevant changes (bug and security fixes) done to `laravie/api`.

## 2.0.5

Released: 2018-08-01

### Added

* Added `Dingo\Api\Transformer\Adapter\Illuminate`.

## 2.0.4

Released: 2018-04-30

### Changes

* Use `Illuminate\Http\Request` as typehint for`Dingo\Api\Http\RateLimit\Handler`.
* Override `Dingo\Api\Routing\ResourceRegistrar::register()` to prevent imcompatibility with breaking changes introduced in Laravel Framework v5.6.17.

## 2.0.3

Released: 2018-04-03

### Added

* Add `Dingo\Api\Routing\Route::getMiddleware()` method. ([@gazben](https://github.com/gazben))

### Fixes

* Fixes `Dingo\Api\Http\FormRequest` compatibility with Lumen and Laravel. ([@gazben](https://github.com/gazben))

## 2.0.2

Released: 2018-03-03

### Changes

* Show `errors` and `status_code` for `Illuminate\Validation\ValidationException`. ([@yangliulnn](https://github.com/yangliulnn))
* Use `Illuminate\Validation\ValidationException::$status` if it's available as HTTP response status code. ([@SamsamBabadi](https://github.com/SamsamBabadi))

## 2.0.1

Released: 2018-02-12

### Changes

* Allows installation for Laravel Framework `5.5.x` and `5.6.x`.

### Fixes

* Fixes Terminating middleware from not being called. ([@yangzuwei](https://github.com/yangzuwei))
* Fixes merging none-API routes. ([@maesklaas](https://github.com/maesklaas))

## 2.0.0

Released: 2017-11-14

### Changes

* Drop support for Laravel Framework `5.4.x`.
* `Dingo\Api\Routing\Route` now extends `Illuminate\Routing\Route`.
