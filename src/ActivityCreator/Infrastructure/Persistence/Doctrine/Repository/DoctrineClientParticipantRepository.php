<?php

namespace ActivityCreator\Infrastructure\Persistence\Doctrine\Repository;

use ActivityCreator\ {
    Application\Service\ClientParticipant\ClientParticipantRepository,
    Domain\DependencyModel\Firm\Client\ProgramParticipation
};
use Doctrine\ORM\ {
    EntityRepository,
    NoResultException
};
use Resources\Exception\RegularException;

class DoctrineClientParticipantRepository extends EntityRepository implements ClientParticipantRepository
{

    public function aProgramParticipationBelongsToClient(
            string $firmId, string $clientId, string $programParticipationId): ProgramParticipation
    {
        $params = [
            "firmId" => $firmId,
            "clientId" => $clientId,
            "programParticipationId" => $programParticipationId,
        ];
        
        $qb = $this->createQueryBuilder("programParticipation");
        $qb->select("programParticipation")
                ->andWhere($qb->expr()->eq("programParticipation.id", ":programParticipationId"))
                ->leftJoin("programParticipation.client", "client")
                ->andWhere($qb->expr()->eq("client.id", ":clientId"))
                ->andWhere($qb->expr()->eq("client.firmId", ":firmId"))
                ->setParameters($params)
                ->setMaxResults(1);
        
        try {
            return $qb->getQuery()->getSingleResult();
        } catch (NoResultException $ex) {
            $errorDetail = "not found: program participation not found";
            throw RegularException::notFound($errorDetail);
        }
    }

}
