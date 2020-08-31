<?php

namespace Participant\Infrastructure\Persistence\Doctrine\Repository;

use Doctrine\ORM\ {
    EntityRepository,
    NoResultException
};
use Participant\ {
    Application\Service\Participant\MissionRepository,
    Domain\Model\DependencyEntity\Firm\Program\Mission
};
use Resources\Exception\RegularException;

class DoctrineMissionRepository extends EntityRepository implements MissionRepository
{

    public function ofId(string $firmId, string $programId, string $missionId): Mission
    {
        $params = [
            'firmId' => $firmId,
            'programId' => $programId,
            'missionId' => $missionId,
        ];

        $qb = $this->createQueryBuilder("mission");
        $qb->select("mission")
                ->andWhere($qb->expr()->eq('mission.id', ":missionId"))
                ->leftJoin("mission.program", "program")
                ->andWhere($qb->expr()->eq('program.id', ":programId"))
                ->andWhere($qb->expr()->eq('program.firmId', ":firmId"))
                ->setParameters($params)
                ->setMaxResults(1);

        try {
            return $qb->getQuery()->getSingleResult();
        } catch (NoResultException $ex) {
            $errorDetail = "not found: mission not found";
            throw RegularException::notFound($errorDetail);
        }
    }

}
