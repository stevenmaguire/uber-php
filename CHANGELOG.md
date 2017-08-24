# Changelog
All Notable changes to `uber-php` will be documented in this file

## 1.6.0 - 2017-08-23

### Added
- Support for driver endpoints

### Deprecated
- Nothing

### Fixed
- Nothing

### Removed
- Nothing

### Security
- Nothing

## 1.5.1 - 2017-08-03

### Added
- Nothing

### Deprecated
- Nothing

### Fixed
- Updated incorrect verb used for updating existing entities.

### Removed
- Nothing

### Security
- Nothing

## 1.5.0 - 2016-11-21

### Added
- Update rate limit parser to fail gracefully when no headers returned, as is expected in Uber API v1.2
- Update default api version to v1.2
- Add profile patch support
- Add payment methods list support
- Add place detail support
- Add update place support
- Add current ride request detail support
- Add update current ride request support
- Update sandbox method names to include intent
- Add update specific request support
- Add create reminder support
- Add fetch reminder support
- Add update reminder support
- Add cancel reminder support
- Add raised exception when invoking sandbox methods on non-sandbox client

### Deprecated
- Removed `setProduct` method, replaced with `setSandboxProduct`

### Fixed
- Nothing

### Removed
- Nothing

### Security
- Nothing

## 1.4.0 - 2015-07-08

### Added
- Ride estimate API support
- Ride receipt API support

### Deprecated
- Nothing

### Fixed
- Nothing

### Removed
- Nothing

### Security
- Nothing

## 1.3.1 - 2015-07-06

### Added
- Corrected namespace for Guzzle 6 upgrade

### Deprecated
- Nothing

### Fixed
- Nothing

### Removed
- Nothing

### Security
- Nothing

## 1.3.0 - 2015-07-03

### Added
- Upgraded Guzzle package to version 6
- Bumped minimum PHP version from 5.4 to 5.5

### Deprecated
- Nothing

### Fixed
- Nothing

### Removed
- Nothing

### Security
- Nothing

## 1.2.0 - 2015-05-28

### Added
- Added support for Sandbox PUT methods

### Deprecated
- Nothing

### Fixed
- Nothing

### Removed
- Nothing

### Security
- Nothing

## 1.1.0 - 2015-05-21

### Added
- Improved handling of HTTP Errors from Uber API
- Added ability to get HTTP Error response body from exception; helpful in "surge confirmation" flow.

### Deprecated
- Nothing

### Fixed
- Nothing

### Removed
- Nothing

### Security
- Nothing

## 1.0.0 - 2015-03-24

### Added
- Uber API v1 and v1.1 Support
- Toggle Sandbox mode
- Check status of rate limiting

### Deprecated
- Nothing

### Fixed
- Nothing

### Removed
- Nothing

### Security
- Nothing
