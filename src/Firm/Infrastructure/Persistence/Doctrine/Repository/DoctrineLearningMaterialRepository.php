<?php

namespace Firm\Infrastructure\Persistence\Doctrine\Repository;

use Doctrine\ORM\NoResultException;
use Firm\Application\Service\Firm\Program\Mission\LearningMaterialRepository;
use Firm\Application\Service\Firm\Program\Mission\MissionCompositionId;
use Firm\Domain\Model\Firm\Program\Mission\LearningMaterial;
use Firm\Domain\Task\Dependency\Firm\Program\Mission\LearningMaterialRepository as LearningMaterialRepository2;
use Resources\Exception\RegularException;
use Resources\Infrastructure\Persistence\Doctrine\Repository\DoctrineEntityRepository;
use Resources\Uuid;

class DoctrineLearningMaterialRepository extends DoctrineEntityRepository implements LearningMaterialRepository, LearningMaterialRepository2
{

    public function add(LearningMaterial $learningMaterial): void
    {
        $em = $this->getEntityManager();
        $em->persist($learningMaterial);
        $em->flush();
    }

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

    public function update(): void
    {
        $this->getEntityManager()->flush();
    }

    public function aLearningMaterialOfId(string $id): LearningMaterial
    {
        return $this->findOneByIdOrDie($id, 'learning material');
    }

}
