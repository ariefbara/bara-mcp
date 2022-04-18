<?php

namespace Client\Infrastructure\Persistence\Doctrine\Repository;

use Client\Domain\Model\Client\ClientParticipant;
use Client\Domain\Task\Repository\Firm\Client\ClientParticipantRepository;
use Resources\Infrastructure\Persistence\Doctrine\Repository\DoctrineEntityRepository;

class DoctrineClientParticipantRepository extends DoctrineEntityRepository implements ClientParticipantRepository
{

    public function add(ClientParticipant $clientParticipant): void
    {
        $this->persist($clientParticipant);
    }

}
