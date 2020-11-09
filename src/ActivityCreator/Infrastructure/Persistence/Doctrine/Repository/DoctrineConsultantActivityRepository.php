<?php

namespace ActivityCreator\Infrastructure\Persistence\Doctrine\Repository;

use ActivityCreator\{
    Application\Service\Consultant\ConsultantActivityRepository,
    Domain\Model\ConsultantActivity
};
use Doctrine\ORM\{
    EntityRepository,
    NoResultException
};
use Resources\{
    Exception\RegularException,
    Uuid
};

class DoctrineConsultantActivityRepository extends EntityRepository implements ConsultantActivityRepository
{

    public function aConsultantActivityBelongsToPersonnel(
            string $firmId, string $personnelId, string $consultantActivityId): ConsultantActivity
    {
        $params = [
            "firmId" => $firmId,
            "personnelId" => $personnelId,
            "consultantActivityId" => $consultantActivityId,
        ];

        $qb = $this->createQueryBuilder("consultantActivity");
        $qb->select("consultantActivity")
                ->andHaving($qb->expr()->eq("consultantActivity.id", ":consultantActivityId"))
                ->leftJoin("consultantActivity.consultant", "consultant")
                ->leftJoin("consultant.personnel", "personnel")
                ->andHaving($qb->expr()->eq("personnel.id", ":personnelId"))
                ->andHaving($qb->expr()->eq("personnel.firmId", ":firmId"))
                ->setParameters($params)
                ->setMaxResults(1);

        try {
            return $qb->getQuery()->getSingleResult();
        } catch (NoResultException $ex) {
            $errorDetail = "not found: consultant activity not found";
            throw RegularException::notFound($errorDetail);
        }
    }

    public function add(ConsultantActivity $consultantActivity): void
    {
        $em = $this->getEntityManager();
        $em->persist($consultantActivity);
        $em->flush();
    }

    public function nextIdentity(): string
    {
        return Uuid::generateUuid4();
    }

    public function update(): void
    {
        $this->getEntityManager()->flush();
    }

}
