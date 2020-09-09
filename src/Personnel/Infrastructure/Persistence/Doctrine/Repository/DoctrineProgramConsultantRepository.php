<?php

namespace Personnel\Infrastructure\Persistence\Doctrine\Repository;

use Doctrine\ORM\ {
    EntityRepository,
    NoResultException
};
use Personnel\ {
    Application\Service\Firm\Personnel\ProgramConsultantRepository,
    Domain\Model\Firm\Personnel\ProgramConsultant
};
use Resources\Exception\RegularException;

class DoctrineProgramConsultantRepository extends EntityRepository implements ProgramConsultantRepository
{

    public function update(): void
    {
        $this->getEntityManager()->flush();
    }

    public function ofId(string $firmId, string $personnelId, string $programConsultationId): ProgramConsultant
    {
        $parameters = [
            "firmId" => $firmId,
            "personnelId" => $personnelId,
            "programConsultantId" => $programConsultationId,
        ];
        
        $qb = $this->createQueryBuilder('programConsultant');
        $qb->select('programConsultant')
                ->andWhere($qb->expr()->eq('programConsultant.id', ':programConsultantId'))
                ->leftJoin("programConsultant.personnel", "personnel")
                ->andWhere($qb->expr()->eq('personnel.id', ':personnelId'))
                ->andWhere($qb->expr()->eq('personnel.firmId', ':firmId'))
                ->setParameters($parameters)
                ->setMaxResults(1);
        
        try {
            return $qb->getQuery()->getSingleResult();
        } catch (NoResultException $ex) {
            $errorDetail = "not found: program consultant not found";
            throw RegularException::notFound($errorDetail);
        }
        
    }

}
