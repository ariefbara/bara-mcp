<?php

namespace Client\Infrastructure\Persistence\Doctrine\Repository;

use Client\Application\Service\Client\BioFormRepository;
use Client\Domain\DependencyModel\Firm\BioForm;
use Doctrine\ORM\EntityRepository;
use Doctrine\ORM\NoResultException;
use Resources\Exception\RegularException;

class DoctrineBioFormRepository extends EntityRepository implements BioFormRepository
{

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
            $errorDetail = "not found: client cv form not found";
            throw RegularException::notFound($errorDetail);
        }
    }

}
