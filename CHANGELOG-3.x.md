# Changelog for 3.x

This changelog references the relevant changes (bug and security fixes) done to `laravie/api`.

## 3.1.0

Released: 2019-12-26

### Changes

* Automatically handle rendering `Laravel\Passport\Exceptions\OAuthServerException` response.

## 3.0.0

Released: 2019-09-23

### Added

* Added `Dingo\Api\Lumen\Decorator\RequestMiddleware` class.
* Added `Dingo\Api\Lumen\Adapter\RequestMiddleware` trait.

### Removed

* Remove support for Internal Request and remove the following classes:
  - `Dingo\Api\Dispatcher`
  - `Dingo\Apo\Facade\API`
* Remove deprecated uses of `Dingo\Api\Console\Command\Routes::fire()`.
