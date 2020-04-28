<?php

namespace Client\Infrastructure\Persistence\Doctrine\Repository;

use Client\ {
    Application\Service\Firm\ProgramRepository,
    Domain\Model\Firm\Program
};
use Doctrine\ORM\ {
    EntityRepository,
    NoResultException
};
use Resources\Exception\RegularException;

class DoctrineProgramRepository extends EntityRepository implements ProgramRepository
{
    
    public function ofId(string $firmId, string $programId): Program
    {
        $params = [
            "programId" => $programId,
            "firmId" => $firmId,
        ];
        $qb = $this->createQueryBuilder('program');
        $qb->select('program')
                ->andWhere($qb->expr()->eq('program.removed', "false"))
                ->andWhere($qb->expr()->eq('program.id', ":programId"))
                ->leftJoin('program.firm', 'firm')
                ->andWhere($qb->expr()->eq('firm.id', ":firmId"))
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
