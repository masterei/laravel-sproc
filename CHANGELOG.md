# Changelog

All notable changes to this project will be documented in this file.

The format is based on [Keep a Changelog](https://keepachangelog.com/en/1.0.0/),
and this project adheres to [Semantic Versioning](https://semver.org/spec/v2.0.0.html).

## [3.0.1] - 2023-09-20

### Fixed
- Dataset hydrate bug.

## [3.0.0] - 2023-01-19

### Changed
- Finalizing namespace changes.

## [2.0.0] - 2022-11-07

### Changed
- Changed in class namespaces.

## [1.1.2] - 2022-08-04

### Changed
- `getLastInsertedId()` changed to `getScopeID()`.
- `execute()` method now only returns void.

## [1.1.1] - 2022-08-02

### Fixed
- Facade Bug: New Object Instance.
- Resolved - `self::clearResolvedInstance()`;

## [1.1.0] - 2022-07-18

### Added
- `hydrate()` and `toSql()` method.
- Please see [README File](README.md) for more information.

### Fixed
- Parameter Binding - Error converting data type nvarchar to datetime.

## [1.0.0] - 2022-07-17

- Initial release.
