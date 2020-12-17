<?php

namespace Participant\Application\Service\Client;

use Participant\Domain\Model\ClientRegistrant;

interface ClientRegistrantRepository
{

    public function aClientRegistrant(string $firmId, string $clientId, string $programRegistrationId): ClientRegistrant;

    public function update(): void;
}
