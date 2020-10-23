<?php

namespace Firm\Infrastructure\Persistence\Doctrine\Repository;

use Doctrine\ORM\EntityRepository;
use Firm\ {
    Application\Service\Firm\Program\ParticipantRepository,
    Domain\Model\Firm\Program\Participant
};
use Resources\Exception\RegularException;

class DoctrineParticipantRepository extends EntityRepository implements ParticipantRepository
{
    
    public function ofId(string $participantId): Participant
    {
        $participant = $this->findOneBy(["id" => $participantId]);
        if (empty($participant)) {
            $errorDetail = "not found: participant not found";
            throw RegularException::notFound($errorDetail);
        }
        return $participant;
    }

    public function update(): void
    {
        $this->getEntityManager()->flush();
    }

}
