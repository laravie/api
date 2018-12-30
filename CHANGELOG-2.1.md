# Changelog for 2.1

This changelog references the relevant changes (bug and security fixes) done to `laravie/api`.

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
