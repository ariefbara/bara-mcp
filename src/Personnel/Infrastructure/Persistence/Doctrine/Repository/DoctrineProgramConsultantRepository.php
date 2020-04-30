<?php

namespace Personnel\Infrastructure\Persistence\Doctrine\Repository;

use Doctrine\ORM\ {
    EntityRepository,
    NoResultException
};
use Personnel\ {
    Application\Service\Firm\Personnel\PersonnelCompositionId,
    Application\Service\Firm\Personnel\ProgramConsultantRepository,
    Domain\Model\Firm\Personnel\ProgramConsultant
};
use Resources\ {
    Exception\RegularException,
    Infrastructure\Persistence\Doctrine\PaginatorBuilder
};

class DoctrineProgramConsultantRepository extends EntityRepository implements ProgramConsultantRepository
{

    public function all(PersonnelCompositionId $personnelCompositionId, int $page, int $pageSize)
    {
        $parameters = [
            "personnelId" => $personnelCompositionId->getPersonnelId(),
            "firmId" => $personnelCompositionId->getFirmId(),
        ];
        
        $qb = $this->createQueryBuilder('programConsultant');
        $qb->select('programConsultant')
                ->andWhere($qb->expr()->eq('programConsultant.removed', 'false'))
                ->leftJoin("programConsultant.personnel", "personnel")
                ->andWhere($qb->expr()->eq('personnel.removed', 'false'))
                ->andWhere($qb->expr()->eq('personnel.id', ':personnelId'))
                ->leftJoin("personnel.firm", "firm")
                ->andWhere($qb->expr()->eq('firm.id', ':firmId'))
                ->setParameters($parameters);
        
        return PaginatorBuilder::build($qb->getQuery(), $page, $pageSize);
        
    }

    public function ofId(PersonnelCompositionId $personnelCompositionId, string $programConsultantId): ProgramConsultant
    {
        $parameters = [
            "programConsultantId" => $programConsultantId,
            "personnelId" => $personnelCompositionId->getPersonnelId(),
            "firmId" => $personnelCompositionId->getFirmId(),
        ];
        
        $qb = $this->createQueryBuilder('programConsultant');
        $qb->select('programConsultant')
                ->andWhere($qb->expr()->eq('programConsultant.removed', 'false'))
                ->andWhere($qb->expr()->eq('programConsultant.id', ':programConsultantId'))
                ->leftJoin("programConsultant.personnel", "personnel")
                ->andWhere($qb->expr()->eq('personnel.removed', 'false'))
                ->andWhere($qb->expr()->eq('personnel.id', ':personnelId'))
                ->leftJoin("personnel.firm", "firm")
                ->andWhere($qb->expr()->eq('firm.id', ':firmId'))
                ->setParameters($parameters)
                ->setMaxResults(1);
        
        try {
            return $qb->getQuery()->getSingleResult();
        } catch (NoResultException $ex) {
            $errorDetail = "not found: program consultant not found";
            throw RegularException::notFound($errorDetail);
        }
        
    }

    public function update(): void
    {
        $this->getEntityManager()->flush();
    }

}
