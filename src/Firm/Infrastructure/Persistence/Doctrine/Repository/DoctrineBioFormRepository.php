<?php

namespace Firm\Infrastructure\Persistence\Doctrine\Repository;

use Doctrine\ORM\NoResultException;
use Firm\Application\Service\Manager\BioFormRepository;
use Firm\Domain\Model\Firm\BioForm;
use Firm\Domain\Task\BioFormRepository as InterfaceForTask;
use Resources\Exception\RegularException;
use Resources\Infrastructure\Persistence\Doctrine\Repository\DoctrineEntityRepository;

class DoctrineBioFormRepository extends DoctrineEntityRepository implements BioFormRepository, InterfaceForTask
{

    public function add(BioForm $bioForm): void
    {
        $this->persist($bioForm);
    }

    public function ofId(string $bioFormId): BioForm
    {
        $params = [
            "id" => $bioFormId,
        ];
        $qb = $this->createQueryBuilder("bioForm");
        $qb->select("bioForm")
                ->leftJoin("bioForm.form", "form")
                ->andWhere($qb->expr()->eq("form.id", ":id"))
                ->setParameters($params)
                ->setMaxResults(1);

        try {
            return $qb->getQuery()->getSingleResult();
        } catch (NoResultException $ex) {
            $errorDetail = "not found: bio form not found";
            throw RegularException::notFound($errorDetail);
        }
    }

}
