<?php

namespace Multek\CustomerEngagement\Contracts;

use Multek\CustomerEngagement\DTOs\Customer;

interface SyncsUsers
{
    public function getUser(string $externalId): array;

    public function createUser(Customer $customer): array;

    public function updateUser(Customer $customer): array;

    public function deleteUser(string $externalId): void;
}
