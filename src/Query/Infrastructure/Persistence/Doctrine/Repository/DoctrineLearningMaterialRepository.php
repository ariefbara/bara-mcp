<?php

namespace Query\Infrastructure\Persistence\Doctrine\Repository;

use Doctrine\ORM\ {
    EntityRepository,
    NoResultException
};
use Query\ {
    Application\Service\Firm\Program\Mission\LearningMaterialRepository,
    Domain\Model\Firm\Program\Mission\LearningMaterial
};
use Resources\ {
    Exception\RegularException,
    Infrastructure\Persistence\Doctrine\PaginatorBuilder
};

class DoctrineLearningMaterialRepository extends EntityRepository implements LearningMaterialRepository
{
    public function all(string $firmId, string $programId, string $missionId, int $page, int $pageSize)
    {
        $params = [
            "firmId" => $firmId,
            "programId" => $programId,
            "missionId" => $missionId,
        ];
        
        $qb = $this->createQueryBuilder("learningMaterial");
        $qb->select("learningMaterial")
                ->andWhere($qb->expr()->eq("learningMaterial.removed", "false"))
                ->leftJoin("learningMaterial.mission", "mission")
                ->andWhere($qb->expr()->eq("mission.id", ":missionId"))
                ->leftJoin("mission.program", "program")
                ->andWhere($qb->expr()->eq("program.id", ":programId"))
                ->leftJoin("program.firm", "firm")
                ->andWhere($qb->expr()->eq("firm.id", ":firmId"))
                ->setParameters($params);
        
        return PaginatorBuilder::build($qb->getQuery(), $page, $pageSize);
    }

    public function ofId(string $firmId, string $programId, string $missionId, string $learningMaterialId): LearningMaterial
    {
        $params = [
            "firmId" => $firmId,
            "programId" => $programId,
            "missionId" => $missionId,
            "learningMaterialId" => $learningMaterialId,
        ];
        
        $qb = $this->createQueryBuilder("learningMaterial");
        $qb->select("learningMaterial")
                ->andWhere($qb->expr()->eq("learningMaterial.id", ":learningMaterialId"))
                ->andWhere($qb->expr()->eq("learningMaterial.removed", "false"))
                ->leftJoin("learningMaterial.mission", "mission")
                ->andWhere($qb->expr()->eq("mission.id", ":missionId"))
                ->leftJoin("mission.program", "program")
                ->andWhere($qb->expr()->eq("program.id", ":programId"))
                ->leftJoin("program.firm", "firm")
                ->andWhere($qb->expr()->eq("firm.id", ":firmId"))
                ->setParameters($params)
                ->setMaxResults(1);
        
        try {
            return $qb->getQuery()->getSingleResult();
        } catch (NoResultException $ex) {
            $errorDetail = "not found: learning material not found";
            throw RegularException::notFound($errorDetail);
        }
    }

}
