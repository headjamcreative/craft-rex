# Craft REX Changelog

All notable changes to this project will be documented in this file.

The format is based on [Keep a Changelog](http://keepachangelog.com/) and this project adheres to [Semantic Versioning](http://semver.org/).

## 1.3.4 - 2026-08-27

### Fixed

- `RexListingService::findAll()` had no `ORDER BY`, so results (and the 100-row cap applied to non-"current" statuses) came back in arbitrary/insertion order rather than by date. Removed the hardcoded `limit(100)` and added `ORDER BY soldDate DESC` (or `publishDate DESC` for "current" status) so results are returned newest-first and in full.

## 1.0.5 - 2020-08-05

### Added

- Initial release
