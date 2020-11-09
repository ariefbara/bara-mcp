<?php

namespace Query\Infrastructure\Persistence\Doctrine\Repository;

use Doctrine\ORM\{
    EntityRepository,
    NoResultException
};
use Query\{
    Application\Service\Firm\Personnel\ProgramConsultant\ConsultantActivityRepository,
    Domain\Model\Firm\Program\Consultant\ConsultantActivity
};
use Resources\{
    Exception\RegularException,
    Infrastructure\Persistence\Doctrine\PaginatorBuilder
};

class DoctrineConsultantActivityRepository extends EntityRepository implements ConsultantActivityRepository
{

    public function allActivitiesBelongsToConsultant(
            string $firmId, string $personnelId, string $consultantId, int $page, int $pageSize)
    {
        $params = [
            "firmId" => $firmId,
            "personnelId" => $personnelId,
            "consultantId" => $consultantId,
        ];

        $qb = $this->createQueryBuilder("consultantActivity");
        $qb->select("consultantActivity")
                ->leftJoin("consultantActivity.consultant", "consultant")
                ->andWhere($qb->expr()->eq("consultant.id", ":consultantId"))
                ->leftJoin("consultant.personnel", "personnel")
                ->andWhere($qb->expr()->eq("personnel.id", ":personnelId"))
                ->leftJoin("personnel.firm", "firm")
                ->andWhere($qb->expr()->eq("firm.id", ":firmId"))
                ->setParameters($params);

        return PaginatorBuilder::build($qb->getQuery(), $page, $pageSize);
    }

    public function anActivityBelongsToConsultant(string $firmId, string $personnelId, string $activityId): ConsultantActivity
    {
        $params = [
            "firmId" => $firmId,
            "personnelId" => $personnelId,
            "activityId" => $activityId,
        ];

        $qb = $this->createQueryBuilder("consultantActivity");
        $qb->select("consultantActivity")
                ->andWhere($qb->expr()->eq("consultantActivity.id", ":activityId"))
                ->leftJoin("consultantActivity.consultant", "consultant")
                ->leftJoin("consultant.personnel", "personnel")
                ->andWhere($qb->expr()->eq("personnel.id", ":personnelId"))
                ->leftJoin("personnel.firm", "firm")
                ->andWhere($qb->expr()->eq("firm.id", ":firmId"))
                ->setParameters($params)
                ->setMaxResults(1);

        try {
            return $qb->getQuery()->getSingleResult();
        } catch (NoResultException $ex) {
            $errorDetail = "not found: activity not found";
            throw RegularException::notFound($errorDetail);
        }
    }

}
