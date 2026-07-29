# Changelog

All notable changes to `multek/laravel-customer-engagement` will be documented in this file.

The format is based on [Keep a Changelog](https://keepachangelog.com/en/1.1.0/),
and this project adheres to [Semantic Versioning](https://semver.org/spec/v2.0.0.html).

## [1.1.1] - 2026-07-29

### Deprecated

- **This package is deprecated and no longer maintained.** It is frozen at the v1.1.0 feature
  set; this release only adds deprecation metadata. Marked `"abandoned": "multek/laravel-onesignal"`
  in `composer.json`, so Composer now emits a replacement hint on install/update. The package
  has been removed from Packagist and the repository is archived read-only.
- **Replacement:** [`multek/laravel-onesignal`](https://github.com/Multek-Company/laravel-onesignal)
  v2.0.0+, which is standalone as of v2.0.0 and no longer depends on this package.

## [1.1.0] - 2026-07-28

### Added

- **First-class profile fields on the `Customer` DTO** ([#4](https://github.com/Multek-Company/laravel-customer-engagement/issues/4)):
  `language` (ISO 639-1), `timezone` (IANA timezone id), and `country` (ISO 3166-1 alpha-2)
  as optional nullable constructor params, also carried through `fromArray()`, `toArray()`,
  and `with()`. Drivers can now map these to native platform profile properties instead of
  burning plan-limited custom tags on profile data — OneSignal, for example, limits data
  tags per user by plan (2 on Free, 10 on Growth, 100 on Professional), while native
  language/timezone/country properties are free on every plan and power built-in features
  like localized content and per-timezone delivery.
- **Overridable profile getters on `HasCustomerEngagement`** ([#4](https://github.com/Multek-Company/laravel-customer-engagement/issues/4)):
  `getEngagementLanguage()`, `getEngagementTimezone()`, `getEngagementCountry()` — all
  default to `null` and are passed through by `toEngagementCustomer()`.
- **Per-driver capability policy** ([#2](https://github.com/Multek-Company/laravel-customer-engagement/issues/2)):
  `drivers.{name}.capabilities` config (keys `users`, `notifications`, `events`). Missing
  keys default to enabled. A disabled capability makes `syncsUsers()` /
  `sendsNotifications()` / `tracksEvents()` return `false` regardless of the driver's
  contracts, and turns guarded manager calls into silent no-ops (with a debug-level log
  line). Lets plan entitlements be handled with a config flip — e.g. OneSignal Free
  rejects custom events with `403`, so `'events' => false` stops the failed jobs without
  a deploy.
- **Opt-in `ENGAGEMENT_ASYNC_SKIP_NULL`** ([#1](https://github.com/Multek-Company/laravel-customer-engagement/issues/1)):
  new `skip_async_when_null` config key (default `false`). When enabled,
  `syncToEngagementAsync()` resolves the effective driver name and skips dispatching
  `SyncCustomer` when it is the null driver, keeping local/test queues quiet. Off by
  default because it trades Horizon observability and changes dispatch semantics for
  test suites asserting on `SyncCustomer`.

All three changes are additive and backwards compatible.

## [1.0.0] - 2026-04-01

### Added

- Initial release: driver-based `EngagementManager`, capability contracts
  (`SyncsUsers`, `SendsNotifications`, `TracksEvents`), `Customer` / `Notification` /
  `CustomerEvent` DTOs, `HasCustomerEngagement` model trait, `EngagementChannel`
  notification channel, `SyncCustomer` queued job, and the built-in null driver.
- Laravel 13 support.

[1.1.0]: https://github.com/Multek-Company/laravel-customer-engagement/compare/v1.0.0...v1.1.0
[1.0.0]: https://github.com/Multek-Company/laravel-customer-engagement/releases/tag/v1.0.0
