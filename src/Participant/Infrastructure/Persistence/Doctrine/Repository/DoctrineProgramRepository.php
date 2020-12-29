<?php

namespace Participant\Infrastructure\Persistence\Doctrine\Repository;

use Doctrine\ORM\ {
    EntityRepository,
    NoResultException
};
use Participant\ {
    Application\Service\Firm\ProgramRepository,
    Domain\DependencyModel\Firm\Program
};
use Resources\Exception\RegularException;

class DoctrineProgramRepository extends EntityRepository implements ProgramRepository
{
    
    public function ofId(string $firmId, string $programId): Program
    {
        $params = [
            "firmId" => $firmId,
            "programId" => $programId,
        ];
        
        $qb = $this->createQueryBuilder("program");
        $qb->select("program")
                ->andWhere($qb->expr()->eq("program.removed", "false"))
                ->andWhere($qb->expr()->eq("program.id", ":programId"))
                ->andWhere($qb->expr()->eq("program.firmId", ":firmId"))
                ->setParameters($params)
                ->setMaxResults(1);
        
        try {
            return $qb->getQuery()->getSingleResult();
        } catch (NoResultException $ex) {
            $errorDetail = "not found: program not found";
            throw RegularException::notFound($errorDetail);
        }
    }

}
