<?php

namespace Query\Infrastructure\Persistence\Doctrine\Repository;

use Doctrine\ORM\ {
    EntityRepository,
    NoResultException
};
use Firm\Application\Service\Firm\Program\Mission\MissionCompositionId;
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

    public function ofId(MissionCompositionId $missionCompositionId, string $learningMaterialId): LearningMaterial
    {
        $qb = $this->createQueryBuilder('learningMaterial');
        $qb->select('learningMaterial')
                ->andWhere($qb->expr()->eq('learningMaterial.removed', 'false'))
                ->andWhere($qb->expr()->eq('learningMaterial.id', ':learningMaterialId'))
                ->setParameter('learningMaterialId', $learningMaterialId)
                ->leftJoin('learningMaterial.mission', 'mission')
                ->andWhere($qb->expr()->eq('mission.id', ':missionId'))
                ->setParameter('missionId', $missionCompositionId->getMissionId())
                ->leftJoin('mission.program', 'program')
                ->andWhere($qb->expr()->eq('program.removed', 'false'))
                ->andWhere($qb->expr()->eq('program.id', ':programId'))
                ->setParameter('programId', $missionCompositionId->getProgramId())
                ->leftJoin('program.firm', 'firm')
                ->andWhere($qb->expr()->eq('firm.id', ':firmId'))
                ->setParameter('firmId', $missionCompositionId->getFirmId())
                ->setMaxResults(1);
        try {
            return $qb->getQuery()->getSingleResult();
        } catch (NoResultException $ex) {
            $errorDetail = 'not found: learning material not found';
            throw RegularException::notFound($errorDetail);
        }
    }

    public function all(MissionCompositionId $missionCompositionId, int $page, int $pageSize)
    {
        $qb = $this->createQueryBuilder('learningMaterial');
        $qb->select('learningMaterial')
                ->andWhere($qb->expr()->eq('learningMaterial.removed', 'false'))
                ->leftJoin('learningMaterial.mission', 'mission')
                ->andWhere($qb->expr()->eq('mission.id', ':missionId'))
                ->setParameter('missionId', $missionCompositionId->getMissionId())
                ->leftJoin('mission.program', 'program')
                ->andWhere($qb->expr()->eq('program.removed', 'false'))
                ->andWhere($qb->expr()->eq('program.id', ':programId'))
                ->setParameter('programId', $missionCompositionId->getProgramId())
                ->leftJoin('program.firm', 'firm')
                ->andWhere($qb->expr()->eq('firm.id', ':firmId'))
                ->setParameter('firmId', $missionCompositionId->getFirmId());

        return PaginatorBuilder::build($qb->getQuery(), $page, $pageSize);
    }

}
