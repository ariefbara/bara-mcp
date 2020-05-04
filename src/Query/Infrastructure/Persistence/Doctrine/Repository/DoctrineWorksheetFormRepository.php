<?php

namespace Query\Infrastructure\Persistence\Doctrine\Repository;

use Doctrine\ORM\ {
    EntityRepository,
    NoResultException
};
use Query\ {
    Application\Service\Firm\WorksheetFormRepository,
    Domain\Model\Firm\WorksheetForm
};
use Resources\ {
    Exception\RegularException,
    Infrastructure\Persistence\Doctrine\PaginatorBuilder
};

class DoctrineWorksheetFormRepository extends EntityRepository implements WorksheetFormRepository
{

    public function all(string $firmId, int $page, int $pageSize)
    {
        $params = [
            "firmId" => $firmId,
        ];
        $qb = $this->createQueryBuilder('worksheetForm');
        $qb->select('worksheetForm')
                ->andWhere($qb->expr()->eq('worksheetForm.removed', "false"))
                ->leftJoin('worksheetForm.firm', 'firm')
                ->andWhere($qb->expr()->eq('firm.id', ":firmId"))
                ->setParameters($params);

        return PaginatorBuilder::build($qb->getQuery(), $page, $pageSize);
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

}
