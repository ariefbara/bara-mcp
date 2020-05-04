<?php

namespace Client\Infrastructure\Persistence\Doctrine\Repository;

use Client\ {
    Application\Listener\ProgramParticipationRepository as InterfaceForListener,
    Application\Service\Client\ProgramParticipationRepository,
    Domain\Model\Client\ProgramParticipation
};
use Doctrine\ORM\ {
    EntityRepository,
    NoResultException
};
use Resources\Exception\RegularException;

class DoctrineProgramParticipationRepository extends EntityRepository implements ProgramParticipationRepository, InterfaceForListener
{

    public function ofId(string $clientId, string $programParticipationId): ProgramParticipation
    {
        $qb = $this->createQueryBuilder('programParticipation');
        $qb->select('programParticipation')
                ->andWhere($qb->expr()->eq('programParticipation.active', 'true'))
                ->andWhere($qb->expr()->eq('programParticipation.id', ':programParticipationId'))
                ->setParameter('programParticipationId', $programParticipationId)
                ->leftJoin('programParticipation.client', 'client')
                ->andWhere($qb->expr()->eq('client.id', ':clientId'))
                ->setParameter('clientId', $clientId)
                ->setMaxResults(1);

        try {
            return $qb->getQuery()->getSingleResult();
        } catch (NoResultException $ex) {
            $errorDetail = "not found: program participation not found";
            throw RegularException::notFound($errorDetail);
        }
    }

    public function update(): void
    {
        $this->getEntityManager()->flush();
    }

    public function aParticipantOfProgram(string $firmId, string $programId, string $participantId): ProgramParticipation
    {
        $parameters = [
            "programParticipationId" => $participantId,
            "programId" => $programId,
            "firmId" => $firmId,
        ];

        $qb = $this->createQueryBuilder('programParticipation');
        $qb->select('programParticipation')
                ->andWhere($qb->expr()->eq('programParticipation.active', 'true'))
                ->andWhere($qb->expr()->eq('programParticipation.id', ':programParticipationId'))
                ->leftJoin('programParticipation.program', 'program')
                ->andWhere($qb->expr()->eq('program.removed', 'false'))
                ->andWhere($qb->expr()->eq('program.id', ':programId'))
                ->leftJoin('program.firm', 'firm')
                ->andWhere($qb->expr()->eq('firm.id', ':firmId'))
                ->setParameters($parameters)
                ->setMaxResults(1);

        try {
            return $qb->getQuery()->getSingleResult();
        } catch (NoResultException $ex) {
            $errorDetail = "not found: program participation not found";
            throw RegularException::notFound($errorDetail);
        }
    }

}
