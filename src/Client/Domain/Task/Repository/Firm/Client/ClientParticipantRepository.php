<?php

namespace Client\Domain\Task\Repository\Firm\Client;

use Client\Domain\Model\Client\ClientParticipant;

interface ClientParticipantRepository
{

    public function add(ClientParticipant $clientParticipant): void;
}
