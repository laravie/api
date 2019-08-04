# Changelog for 2.2

This changelog references the relevant changes (bug and security fixes) done to `laravie/api`.

## 2.2.2

Released: 2019-08-04

### Changes

* Validate FormRequest when resolving `Illuminate\Contracts\Validation\ValidatesWhenResolved`.
* Use `static function` rather than `function` whenever possible, the PHP engine does not need to instantiate and later GC a `$this` variable for said closure.

## 2.2.1

Released: 2019-06-11

### Changes

* Mark `Dingo\Api\Provider\DingoServiceProvider` as `abstract` and add `validateApiConfiguration()` and `loadApiConfiguration()` methods.

### Fixes

* Fixes loading configuration for Lumen.

## 2.2.0

Released: 2019-03-26

### Changes

* Drop support for Laravel Framework `5.7.x`.
