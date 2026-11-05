# Craft REX Changelog

All notable changes to this project will be documented in this file.

The format is based on [Keep a Changelog](http://keepachangelog.com/) and this project adheres to [Semantic Versioning](http://semver.org/).

## 1.4.0 - 2026-11-06

### Added

- `RexListingService::countAll()` returns the total number of stored listings matching a status/type, ignoring the 100-row cap applied by `findAll()`.
- `RexListingService::findAll()` and `craft.craftrex.listings()` accept an optional `$limit` that overrides the default cap for any status (`0` = no limit). The `stored-results` action accepts a matching `limit` query param.
- `RexListingService::DEFAULT_LIMIT` (100) replaces the hardcoded cap.
- `actionStoredResults()` now sends an `X-Total-Count` response header with that total. The response body is unchanged.

## 1.3.4 - 2026-08-27

### Fixed

- `RexListingService::findAll()` had no `ORDER BY`, so results (and the 100-row cap applied to non-"current" statuses) came back in arbitrary/insertion order rather than by date. Added `ORDER BY soldDate DESC` (or `publishDate DESC` for "current" status) so the capped results are returned newest-first. _Correction (1.4.0): the 100-row cap for non-"current" statuses was never removed in 1.3.4 and remains the default._

## 1.0.5 - 2020-08-05

### Added

- Initial release
