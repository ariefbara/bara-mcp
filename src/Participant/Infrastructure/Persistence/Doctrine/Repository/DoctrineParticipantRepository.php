<?php

namespace Participant\Infrastructure\Persistence\Doctrine\Repository;

use Doctrine\ORM\EntityRepository;
use Participant\ {
    Application\Service\Participant\ParticipantRepository,
    Domain\Model\Participant
};

class DoctrineParticipantRepository extends EntityRepository implements ParticipantRepository
{
    
    public function ofId(string $participantId): Participant
    {
        return $this->findOneBy(["id" => $participantId]);
    }

}
