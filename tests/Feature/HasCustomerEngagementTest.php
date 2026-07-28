<?php

use Multek\CustomerEngagement\Concerns\HasCustomerEngagement;

function fakeEngagementUser(): object
{
    return new class
    {
        use HasCustomerEngagement;

        public string $email = 'user@example.com';

        public string $phone = '+5511999999999';

        public string $name = 'Test User';

        public function getKey(): string
        {
            return 'user-1';
        }
    };
}

it('defaults engagement profile getters to null', function () {
    $user = fakeEngagementUser();

    expect($user->getEngagementLanguage())->toBeNull()
        ->and($user->getEngagementTimezone())->toBeNull()
        ->and($user->getEngagementCountry())->toBeNull();
});

it('passes profile getters through toEngagementCustomer', function () {
    $user = new class
    {
        use HasCustomerEngagement;

        public function getKey(): string
        {
            return 'user-2';
        }

        public function getEngagementLanguage(): ?string
        {
            return 'pt';
        }

        public function getEngagementTimezone(): ?string
        {
            return 'America/Sao_Paulo';
        }

        public function getEngagementCountry(): ?string
        {
            return 'BR';
        }
    };

    $customer = $user->toEngagementCustomer();

    expect($customer->language)->toBe('pt')
        ->and($customer->timezone)->toBe('America/Sao_Paulo')
        ->and($customer->country)->toBe('BR');
});

it('builds a customer with null profile fields by default', function () {
    $customer = fakeEngagementUser()->toEngagementCustomer();

    expect($customer->language)->toBeNull()
        ->and($customer->timezone)->toBeNull()
        ->and($customer->country)->toBeNull();
});
