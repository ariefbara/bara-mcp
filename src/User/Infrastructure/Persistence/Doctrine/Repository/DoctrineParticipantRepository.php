<?php

namespace User\Infrastructure\Persistence\Doctrine\Repository;

use Doctrine\ORM\EntityRepository;
use Resources\Exception\RegularException;
use User\{
    Application\Service\Personnel\Coordinator\ParticipantRepository,
    Domain\DependencyModel\Firm\Program\Participant
};

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

}
