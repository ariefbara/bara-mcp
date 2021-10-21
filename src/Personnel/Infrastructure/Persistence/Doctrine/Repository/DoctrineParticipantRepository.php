<?php

namespace Personnel\Infrastructure\Persistence\Doctrine\Repository;

use Personnel\Domain\Model\Firm\Program\Participant;
use Personnel\Domain\Task\Dependency\Firm\Program\ParticipantRepository;
use Resources\Infrastructure\Persistence\Doctrine\Repository\DoctrineEntityRepository;

class DoctrineParticipantRepository extends DoctrineEntityRepository implements ParticipantRepository
{

    public function ofId(string $id): Participant
    {
        return $this->findOneByIdOrDie($id, 'participant');
    }

}
