<?php

namespace Firm\Infrastructure\Persistence\Doctrine\Repository;

use Doctrine\ORM\ {
    EntityRepository,
    NoResultException
};
use Firm\ {
    Application\Service\Client\ProgramParticipant\ClientParticipantRepository,
    Domain\Model\Firm\Program\ClientParticipant
};
use Resources\Exception\RegularException;

class DoctrineClientParticipantRepository extends EntityRepository implements ClientParticipantRepository
{
    
    public function aClientParticipantCorrespondWithProgram(string $firmId, string $clientId, string $programId): ClientParticipant
    {
        $params = [
            "firmId" => $firmId,
            "clientId" => $clientId,
            "programId" => $programId,
        ];
        
        $qb = $this->createQueryBuilder("clientParticipant");
        $qb->select("clientParticipant")
                ->leftJoin("clientParticipant.client", "client")
                ->andWhere($qb->expr()->eq("client.id", ":clientId"))
                ->leftJoin("clientParticipant.participant", "participant")
                ->leftJoin("participant.program", "program")
                ->andWhere($qb->expr()->eq("program.id", ":programId"))
                ->leftJoin("program.firm", "firm")
                ->andWhere($qb->expr()->eq("firm.id", ":firmId"))
                ->setParameters($params)
                ->setMaxResults(1);
        
        try {
            return $qb->getQuery()->getSingleResult();
        } catch (NoResultException $ex) {
            $errorDetail = "not found: program participant not found";
            throw RegularException::notFound($errorDetail);
        }
    }

}
