<?php

namespace Firm\Infrastructure\Persistence\Doctrine\Repository;

use Doctrine\ORM\EntityRepository;
use Doctrine\ORM\NoResultException;
use Firm\Application\Service\Firm\WorksheetFormRepository;
use Firm\Application\Service\Manager\WorksheetFormRepository as InterfaceForManager;
use Firm\Domain\Model\Firm\WorksheetForm;
use Firm\Domain\Task\Dependency\Firm\WorksheetFormRepository as WorksheetFormRepository2;
use Resources\Exception\RegularException;
use Resources\Uuid;

class DoctrineWorksheetFormRepository extends EntityRepository implements WorksheetFormRepository, InterfaceForManager, WorksheetFormRepository2
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

    public function aWorksheetFormOfId(string $worksheetFormId): WorksheetForm
    {
        $worksheetForm = $this->findOneBy(["id" => $worksheetFormId]);
        if (empty($worksheetForm)) {
            $errorDetail = "not found: worksheet form not found";
            throw RegularException::notFound($errorDetail);
        }
        return $worksheetForm;
    }

}
