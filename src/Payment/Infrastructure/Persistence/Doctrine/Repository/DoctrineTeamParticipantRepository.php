<?php

namespace Payment\Infrastructure\Persistence\Doctrine\Repository;

use Payment\Application\Listener\TeamParticipantRepository;
use Payment\Domain\Model\Firm\Team\TeamParticipant;
use Resources\Infrastructure\Persistence\Doctrine\Repository\DoctrineEntityRepository;

class DoctrineTeamParticipantRepository extends DoctrineEntityRepository implements TeamParticipantRepository
{

    public function ofId(string $id): ?TeamParticipant
    {
        return $this->find($id);
    }

}
