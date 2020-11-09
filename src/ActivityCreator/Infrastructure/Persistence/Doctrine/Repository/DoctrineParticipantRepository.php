<?php

namespace ActivityCreator\Infrastructure\Persistence\Doctrine\Repository;

use ActivityCreator\Domain\{
    DependencyModel\Firm\Program\Participant,
    service\ParticipantRepository as InterfaceForDomainService
};
use Doctrine\ORM\EntityRepository;
use Resources\Exception\RegularException;

class DoctrineParticipantRepository extends EntityRepository implements InterfaceForDomainService
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
