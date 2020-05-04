<?php

namespace Firm\Infrastructure\Persistence\Doctrine\Repository;

use Doctrine\ORM\ {
    EntityRepository,
    NoResultException
};
use Firm\ {
    Application\Service\Firm\WorksheetFormRepository,
    Domain\Model\Firm\WorksheetForm
};
use Resources\ {
    Exception\RegularException,
    Uuid
};

class DoctrineWorksheetFormRepository extends EntityRepository implements WorksheetFormRepository
{

    public function add(WorksheetForm $worksheetForm): void
    {
        $em = $this->getEntityManager();
        $em->persist($worksheetForm);
        $em->flush();
    }

    public function nextIdentity(): string
    {
        return Uuid::generateUuid4();
    }

    public function ofId(string $firmId, string $worksheetFormId): WorksheetForm
    {
        $params = [
            "firmId" => $firmId,
            "worksheetFormId" => $worksheetFormId,
        ];
        $qb = $this->createQueryBuilder('worksheetForm');
        $qb->select('worksheetForm')
                ->andWhere($qb->expr()->eq('worksheetForm.id', ":worksheetFormId"))
                ->andWhere($qb->expr()->eq('worksheetForm.removed', "false"))
                ->leftJoin('worksheetForm.firm', 'firm')
                ->andWhere($qb->expr()->eq('firm.id', ":firmId"))
                ->setParameters($params)
                ->setMaxResults(1);
        
        try {
            return $qb->getQuery()->getSingleResult();
        } catch (NoResultException $ex) {
            $errorDetail = "not found: worksheet form not found";
            throw RegularException::notFound($errorDetail);
        }
    }

    public function update(): void
    {
        $this->getEntityManager()->flush();
    }

}
