<?php

use Multek\CustomerEngagement\DTOs\Customer;

it('accepts language, timezone and country as constructor params', function () {
    $customer = new Customer(
        externalId: 'abc-123',
        language: 'pt',
        timezone: 'America/Sao_Paulo',
        country: 'BR',
    );

    expect($customer->language)->toBe('pt')
        ->and($customer->timezone)->toBe('America/Sao_Paulo')
        ->and($customer->country)->toBe('BR');
});

it('defaults profile fields to null', function () {
    $customer = new Customer(externalId: 'abc-123');

    expect($customer->language)->toBeNull()
        ->and($customer->timezone)->toBeNull()
        ->and($customer->country)->toBeNull();
});

it('hydrates profile fields from array', function () {
    $customer = Customer::fromArray([
        'external_id' => 'abc-123',
        'language' => 'pt',
        'timezone' => 'America/Sao_Paulo',
        'country' => 'BR',
    ]);

    expect($customer->language)->toBe('pt')
        ->and($customer->timezone)->toBe('America/Sao_Paulo')
        ->and($customer->country)->toBe('BR');
});

it('includes profile fields in toArray', function () {
    $customer = new Customer(
        externalId: 'abc-123',
        language: 'pt',
        timezone: 'America/Sao_Paulo',
        country: 'BR',
    );

    expect($customer->toArray())->toMatchArray([
        'language' => 'pt',
        'timezone' => 'America/Sao_Paulo',
        'country' => 'BR',
    ]);
});

it('overrides and preserves profile fields via with', function () {
    $customer = new Customer(
        externalId: 'abc-123',
        language: 'pt',
        timezone: 'America/Sao_Paulo',
        country: 'BR',
    );

    $overridden = $customer->with(['language' => 'en', 'country' => null]);

    expect($overridden->language)->toBe('en')
        ->and($overridden->timezone)->toBe('America/Sao_Paulo')
        ->and($overridden->country)->toBeNull();
});
