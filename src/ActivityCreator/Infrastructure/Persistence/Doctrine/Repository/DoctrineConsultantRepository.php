<?php

namespace ActivityCreator\Infrastructure\Persistence\Doctrine\Repository;

use ActivityCreator\ {
    Application\Service\Consultant\ConsultantRepository,
    Domain\DependencyModel\Firm\Personnel\Consultant,
    Domain\service\ConsultantRepository as InterfaceForDomainService
};
use Doctrine\ORM\ {
    EntityRepository,
    NoResultException
};
use Resources\Exception\RegularException;

class DoctrineConsultantRepository extends EntityRepository implements ConsultantRepository, InterfaceForDomainService
{

    public function aConsultantBelongsToPersonnel(string $firmId, string $personnelId, string $consultantId): Consultant
    {
        $params = [
            "firmId" => $firmId,
            "personnelId" => $personnelId,
            "consultantId" => $consultantId,
        ];

        $qb = $this->createQueryBuilder("consultant");
        $qb->select("consultant")
                ->andWhere($qb->expr()->eq("consultant.id", ":consultantId"))
                ->leftJoin("consultant.personnel", "personnel")
                ->andWhere($qb->expr()->eq("personnel.id", ":personnelId"))
                ->andWhere($qb->expr()->eq("personnel.firmId", ":firmId"))
                ->setParameters($params)
                ->setMaxResults(1);

        try {
            return $qb->getQuery()->getSingleResult();
        } catch (NoResultException $ex) {
            $errorDetail = "not found: consultant not found";
            throw RegularException::notFound($errorDetail);
        }
    }

    public function ofId(string $consultantId): Consultant
    {
        $consultant = $this->findOneBy(["id" => $consultantId]);
        if (empty($consultant)) {
            $errorDetail = "not found: consultant not found";
            throw RegularException::notFound($errorDetail);
        }
        return $consultant;
    }

}
