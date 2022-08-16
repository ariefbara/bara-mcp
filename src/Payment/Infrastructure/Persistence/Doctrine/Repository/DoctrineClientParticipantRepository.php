<?php

namespace Payment\Infrastructure\Persistence\Doctrine\Repository;

use Payment\Application\Listener\ClientParticipantRepository;
use Payment\Domain\Model\Firm\Client\ClientParticipant;
use Resources\Infrastructure\Persistence\Doctrine\Repository\DoctrineEntityRepository;

class DoctrineClientParticipantRepository extends DoctrineEntityRepository implements ClientParticipantRepository
{

    public function ofId(string $id): ?ClientParticipant
    {
        return $this->find($id);
    }

}
