<?php

namespace Query\Infrastructure\Persistence\Doctrine\Repository;

use Doctrine\ORM\{
    EntityRepository,
    NoResultException
};
use Firm\Application\Service\Firm\Program\ProgramCompositionId;
use Query\{
    Application\Service\Firm\Program\MissionRepository,
    Domain\Model\Firm\Program\Mission
};
use Resources\{
    Exception\RegularException,
    Infrastructure\Persistence\Doctrine\PaginatorBuilder
};

class DoctrineMissionRepository extends EntityRepository implements MissionRepository
{

    public function ofId(ProgramCompositionId $programCompositionId, string $missionId): Mission
    {
        $qb = $this->createQueryBuilder('mission');
        $qb->select('mission')
                ->andWhere($qb->expr()->eq('mission.id', ':missionId'))
                ->setParameter('missionId', $missionId)
                ->leftJoin('mission.program', 'program')
                ->andWhere($qb->expr()->eq('program.removed', 'false'))
                ->andWhere($qb->expr()->eq('program.id', ':programId'))
                ->setParameter('programId', $programCompositionId->getProgramId())
                ->leftJoin('program.firm', 'firm')
                ->andWhere($qb->expr()->eq('firm.id', ':firmId'))
                ->setParameter('firmId', $programCompositionId->getFirmId())
                ->setMaxResults(1);

        try {
            return $qb->getQuery()->getSingleResult();
        } catch (NoResultException $ex) {
            $errorDetail = 'not found: mission not found';
            throw RegularException::notFound($errorDetail);
        }
    }

    public function all(ProgramCompositionId $programCompositionId, int $page, int $pageSize)
    {
        $qb = $this->createQueryBuilder('mission');
        $qb->select('mission')
                ->leftJoin('mission.program', 'program')
                ->andWhere($qb->expr()->eq('program.removed', 'false'))
                ->andWhere($qb->expr()->eq('program.id', ':programId'))
                ->setParameter('programId', $programCompositionId->getProgramId())
                ->leftJoin('program.firm', 'firm')
                ->andWhere($qb->expr()->eq('firm.id', ':firmId'))
                ->setParameter('firmId', $programCompositionId->getFirmId());

        return PaginatorBuilder::build($qb->getQuery(), $page, $pageSize);
    }

}
