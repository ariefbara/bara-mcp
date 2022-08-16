<?php

namespace Firm\Domain\Task\Dependency\Firm\Client;

use Firm\Domain\Model\Firm\Client\ClientParticipant;

interface ClientParticipantRepository
{

    public function nextIdentity(): string;

    public function add(ClientParticipant $clientParticipant): void;
}
