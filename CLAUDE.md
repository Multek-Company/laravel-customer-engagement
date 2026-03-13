# CLAUDE.md

This file provides guidance to Claude Code (claude.ai/code) when working with code in this repository.

## Package Overview

A unified customer engagement platform for Laravel — provides contracts, DTOs, and a manager for push notifications, user sync, and event tracking across multiple providers (OneSignal, Braze, Firebase, etc.).

## Development Commands

```bash
# Install dependencies
composer install

# Run all tests
./vendor/bin/pest

# Run a single test file
./vendor/bin/pest tests/Feature/SomeTest.php

# Run a specific test by name
./vendor/bin/pest --filter "test name"

# Code style fix
./vendor/bin/pint
```

## Directory Structure

```
src/
├── Channels/              # Notification channels
├── Concerns/              # Shared traits
├── Contracts/             # Interfaces for driver implementations
├── CustomerEngagementServiceProvider.php
├── DTOs/                  # Data transfer objects
├── EngagementManager.php  # Main manager (driver resolver)
├── Events/                # Laravel events
├── Facades/               # Engagement facade
├── Jobs/                  # Async jobs
└── NullDriver.php         # No-op driver for testing

tests/                     # Pest test suite
```

## Architecture

- **Namespace**: `Multek\CustomerEngagement`
- **ServiceProvider**: `Multek\CustomerEngagement\CustomerEngagementServiceProvider`
- **Facade**: `Multek\CustomerEngagement\Facades\Engagement`
- **Driver pattern**: `EngagementManager` resolves drivers via contracts. Drivers (like OneSignal) are separate packages.
- **NullDriver**: Used as default/testing driver when no provider is configured.

### Key Contracts

Drivers must implement the contracts in `src/Contracts/` for push notifications, user sync, and event tracking.

## Testing Guidelines

- Use Pest framework with Orchestra Testbench
- Use NullDriver for testing without external API calls
- Test driver resolution, notification dispatch, and event tracking
