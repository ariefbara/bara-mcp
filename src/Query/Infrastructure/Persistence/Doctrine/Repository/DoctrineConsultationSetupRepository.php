<?php

namespace Query\Infrastructure\Persistence\Doctrine\Repository;

use Doctrine\ORM\{
    EntityRepository,
    NoResultException
};
use Firm\Application\Service\Firm\Program\ProgramCompositionId;
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

    public function ofId(ProgramCompositionId $programCompositionId, string $consultationSetupId): ConsultationSetup
    {
        $qb = $this->createQueryBuilder('consultationSetup');
        $qb->select('consultationSetup')
                ->andWhere($qb->expr()->eq('consultationSetup.removed', 'false'))
                ->andWhere($qb->expr()->eq('consultationSetup.id', ':consultationSetupId'))
                ->setParameter('consultationSetupId', $consultationSetupId)
                ->leftJoin('consultationSetup.program', 'program')
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
            $errorDetail = 'not found: consultationSetup not found';
            throw RegularException::notFound($errorDetail);
        }
    }

    public function all(ProgramCompositionId $programCompositionId, int $page, int $pageSize)
    {
        $qb = $this->createQueryBuilder('consultationSetup');
        $qb->select('consultationSetup')
                ->andWhere($qb->expr()->eq('consultationSetup.removed', 'false'))
                ->leftJoin('consultationSetup.program', 'program')
                ->andWhere($qb->expr()->eq('program.removed', 'false'))
                ->andWhere($qb->expr()->eq('program.id', ':programId'))
                ->setParameter('programId', $programCompositionId->getProgramId())
                ->leftJoin('program.firm', 'firm')
                ->andWhere($qb->expr()->eq('firm.id', ':firmId'))
                ->setParameter('firmId', $programCompositionId->getFirmId());

        return PaginatorBuilder::build($qb->getQuery(), $page, $pageSize);
    }

}
