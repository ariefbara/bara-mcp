<?php

namespace Firm\Infrastructure\Persistence\Doctrine\Repository;

use Doctrine\ORM\EntityRepository;
use Doctrine\ORM\NoResultException;
use Firm\Application\Service\Firm\Program\ConsultationSetupRepository;
use Firm\Application\Service\Firm\Program\ProgramCompositionId;
use Firm\Application\Service\Manager\ConsultationSetupRepository as InterfaceForManager;
use Firm\Domain\Model\Firm\Program\ConsultationSetup;
use Resources\Exception\RegularException;
use Resources\Uuid;

class DoctrineConsultationSetupRepository extends EntityRepository implements ConsultationSetupRepository, InterfaceForManager
{

    public function add(ConsultationSetup $consultationSetup): void
    {
        $em = $this->getEntityManager();
        $em->persist($consultationSetup);
        $em->flush();
    }

    public function nextIdentity(): string
    {
        return Uuid::generateUuid4();
    }

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

    public function update(): void
    {
        $this->getEntityManager()->flush();
    }

    public function aConsultationSetupOfId(string $consultationSetupId): ConsultationSetup
    {
        $consultationSetup = $this->findOneBy(["id" => $consultationSetupId]);
        if (empty($consultationSetup)) {
            $errorDetail = "not found: consultation setup not found";
            throw RegularException::notFound($errorDetail);
        }
        return $consultationSetup;
    }

}
