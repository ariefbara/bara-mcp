<?php

namespace Query\Infrastructure\Persistence\Doctrine\Repository;

use Doctrine\ORM\EntityRepository;
use Doctrine\ORM\NoResultException;
use Query\Application\Service\Firm\Program\Mission\LearningMaterialRepository;
use Query\Domain\Model\Firm\Program\Mission\LearningMaterial;
use Query\Domain\Service\LearningMaterialRepository as InterfaceForDomainService;
use Query\Domain\Task\Dependency\Firm\Program\Mission\LearningMaterialFilter;
use Query\Domain\Task\Dependency\Firm\Program\Mission\LearningMaterialRepository as LearningMaterialRepository2;
use Resources\Exception\RegularException;
use Resources\Infrastructure\Persistence\Doctrine\PaginatorBuilder;

class DoctrineLearningMaterialRepository extends EntityRepository implements LearningMaterialRepository, InterfaceForDomainService,
        LearningMaterialRepository2
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

    public function aLearningMaterialBelongsToProgram(string $programId, string $learningMaterialId): LearningMaterial
    {
        $params = [
            "programId" => $programId,
            "learningMaterialId" => $learningMaterialId,
        ];

        $qb = $this->createQueryBuilder("learningMaterial");
        $qb->select("learningMaterial")
                ->andWhere($qb->expr()->eq("learningMaterial.id", ":learningMaterialId"))
                ->leftJoin("learningMaterial.mission", "mission")
                ->leftJoin("mission.program", "program")
                ->andWhere($qb->expr()->eq("program.id", ":programId"))
                ->setParameters($params)
                ->setMaxResults(1);

        try {
            return $qb->getQuery()->getSingleResult();
        } catch (NoResultException $ex) {
            $errorDetail = "not found: learning material not found";
            throw RegularException::notFound($errorDetail);
        }
    }

    public function aLearningMaterialInFirm(string $firmId, string $id): LearningMaterial
    {
        $params = [
            'firmId' => $firmId,
            'id' => $id,
        ];
        
        $qb = $this->createQueryBuilder('learningMaterial');
        $qb->select('learningMaterial')
                ->andWhere($qb->expr()->eq('learningMaterial.id', ':id'))
                ->leftJoin('learningMaterial.mission', 'mission')
                ->leftJoin('mission.program', 'program')
                ->leftJoin('program.firm', 'firm')
                ->andWhere($qb->expr()->eq('firm.id', ':firmId'))
                ->setMaxResults(1)
                ->setParameters($params);
        
        try {
            return $qb->getQuery()->getSingleResult();
        } catch (NoResultException $ex) {
            throw RegularException::notFound('not found: learning material not found');
        }
    }

    public function allLearningMaterialInFirm(string $firmId, LearningMaterialFilter $filter)
    {
        $params = [
            'firmId' => $firmId,
        ];
        
        $qb = $this->createQueryBuilder('learningMaterial');
        $qb->select('learningMaterial')
                ->leftJoin('learningMaterial.mission', 'mission')
                ->leftJoin('mission.program', 'program')
                ->leftJoin('program.firm', 'firm')
                ->andWhere($qb->expr()->eq('firm.id', ':firmId'))
                ->setParameters($params);
        
        if (!empty($filter->getMissionId())) {
            $qb->andWhere($qb->expr()->eq('mission.id', ':missionId'))
                    ->setParameter('missionId', $filter->getMissionId());
        }
        
        $page = $filter->getPagination()->getPage();
        $pageSize = $filter->getPagination()->getPageSize();
        return PaginatorBuilder::build($qb->getQuery(), $page, $pageSize);
    }

}
