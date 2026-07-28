<?php

use Illuminate\Support\Facades\Bus;
use Multek\CustomerEngagement\Concerns\HasCustomerEngagement;
use Multek\CustomerEngagement\Jobs\SyncCustomer;

function asyncUser(): object
{
    return new class
    {
        use HasCustomerEngagement;

        public function getKey(): string
        {
            return 'user-async';
        }
    };
}

it('dispatches SyncCustomer on the null driver by default', function () {
    Bus::fake();
    config()->set('customer-engagement.default', 'null');

    asyncUser()->syncToEngagementAsync();

    Bus::assertDispatched(SyncCustomer::class);
});

it('skips dispatch when the flag is on and the default driver is null', function () {
    Bus::fake();
    config()->set('customer-engagement.default', 'null');
    config()->set('customer-engagement.skip_async_when_null', true);

    asyncUser()->syncToEngagementAsync();

    Bus::assertNotDispatched(SyncCustomer::class);
});

it('skips dispatch when the flag is on and the null driver is named explicitly', function () {
    Bus::fake();
    config()->set('customer-engagement.default', 'other');
    config()->set('customer-engagement.skip_async_when_null', true);

    asyncUser()->syncToEngagementAsync('null');

    Bus::assertNotDispatched(SyncCustomer::class);
});

it('still dispatches when the flag is on but the driver is not null', function () {
    Bus::fake();
    config()->set('customer-engagement.default', 'onesignal');
    config()->set('customer-engagement.skip_async_when_null', true);

    asyncUser()->syncToEngagementAsync();

    Bus::assertDispatched(SyncCustomer::class);
});
