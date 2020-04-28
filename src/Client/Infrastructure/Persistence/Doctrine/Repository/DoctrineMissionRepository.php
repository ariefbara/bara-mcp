<?php

namespace Client\Infrastructure\Persistence\Doctrine\Repository;

use Client\ {
    Application\Service\Firm\Program\MissionRepository,
    Domain\Model\Client\ProgramParticipation,
    Domain\Model\Firm\Program\Mission
};
use Doctrine\ORM\ {
    EntityRepository,
    NoResultException
};
use Resources\Exception\RegularException;

class DoctrineMissionRepository extends EntityRepository implements MissionRepository
{
    
    public function ofId(string $clientId, string $programParticipationId, string $missionId): Mission
    {
        $parameters = [
            "missionId" => $missionId,
            "programParticipationId" => $programParticipationId,
            "clientId" => $clientId,
        ];
        $subQuery = $this->getEntityManager()->createQueryBuilder();
        $subQuery->select('tProgram.id')
                ->from(ProgramParticipation::class, "programParticipation")
                ->andWhere($subQuery->expr()->eq('programParticipation.active', "true"))
                ->andWhere($subQuery->expr()->eq('programParticipation.id', ":programParticipationId"))
                ->leftJoin('programParticipation.client', "client")
                ->andWhere($subQuery->expr()->eq('client.id', ":clientId"))
                ->leftJoin('programParticipation.program', "tProgram")
                ->setMaxResults(1);
        
        $qb = $this->createQueryBuilder("mission");
        $qb->select("mission")
                ->andWhere($qb->expr()->eq('mission.id', ":missionId"))
                ->leftJoin("mission.program", "program")
                ->andWhere($qb->expr()->in('program.id', $subQuery->getDQL()))
                ->setParameters($parameters)
                ->setMaxResults(1);
        
        try {
            return $qb->getQuery()->getSingleResult();
        } catch (NoResultException $ex) {
            $errorDetail = "not found: mission not found";
            throw RegularException::notFound($errorDetail);
        }
    }

}
