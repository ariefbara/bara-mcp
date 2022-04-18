<?php

namespace Client\Infrastructure\Persistence\Doctrine\Repository;

use Client\Domain\DependencyModel\Firm\Program\Participant;
use Client\Domain\Task\Repository\Firm\Program\ParticipantRepository;
use Resources\Infrastructure\Persistence\Doctrine\Repository\DoctrineEntityRepository;

class DoctrineParticipantRepository extends DoctrineEntityRepository implements ParticipantRepository
{

    public function ofId(string $id): Participant
    {
        return $this->findOneByIdOrDie($id, 'participant');
    }

}
