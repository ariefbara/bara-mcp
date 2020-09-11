<?php

namespace Query\Infrastructure\Persistence\Doctrine\Repository;

use Doctrine\ORM\{
    EntityRepository,
    NoResultException
};
use Query\{
    Application\Service\Firm\Program\ConsultationSetupRepository,
    Domain\Model\Firm\Program\ConsultationSetup
};
use Resources\{
    Exception\RegularException,
    Infrastructure\Persistence\Doctrine\PaginatorBuilder
};

class DoctrineConsultationSetupRepository extends EntityRepository implements ConsultationSetupRepository
{

    public function all(string $firmId, string $programId, int $page, int $pageSize)
    {
        $params = [
            "firmId" => $firmId,
            "programId" => $programId,
        ];

        $qb = $this->createQueryBuilder("consultationSetup");
        $qb->select("consultationSetup")
                ->andWhere($qb->expr()->eq("consultationSetup.removed", "false"))
                ->leftJoin("consultationSetup.program", "program")
                ->andWhere($qb->expr()->eq("program.id", ":programId"))
                ->leftJoin("program.firm", "firm")
                ->andWhere($qb->expr()->eq("firm.id", ":firmId"))
                ->setParameters($params);

        return PaginatorBuilder::build($qb->getQuery(), $page, $pageSize);
    }

    public function ofId(string $firmId, string $programId, string $consultationSetupId): ConsultationSetup
    {
        $params = [
            "firmId" => $firmId,
            "programId" => $programId,
            "consultationSetupId" => $consultationSetupId,
        ];

        $qb = $this->createQueryBuilder("consultationSetup");
        $qb->select("consultationSetup")
                ->andWhere($qb->expr()->eq("consultationSetup.id", ":consultationSetupId"))
                ->andWhere($qb->expr()->eq("consultationSetup.removed", "false"))
                ->leftJoin("consultationSetup.program", "program")
                ->andWhere($qb->expr()->eq("program.id", ":programId"))
                ->leftJoin("program.firm", "firm")
                ->andWhere($qb->expr()->eq("firm.id", ":firmId"))
                ->setParameters($params)
                ->setMaxResults(1);

        try {
            return $qb->getQuery()->getSingleResult();
        } catch (NoResultException $ex) {
            $errorDetail = "not found: consultation setup not found";
            throw RegularException::notFound($errorDetail);
        }
    }

}
