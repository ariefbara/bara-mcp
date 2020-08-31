<?php

namespace User\Infrastructure\Persistence\Doctrine\Repository;

use User\ {
    Application\Listener\ProgramParticipationRepository as InterfaceForListener,
    Application\Service\User\ProgramParticipationRepository,
    Domain\Model\User\ProgramParticipation
};
use Doctrine\ORM\ {
    EntityRepository,
    NoResultException
};
use Resources\Exception\RegularException;

class DoctrineProgramParticipationRepository extends EntityRepository implements ProgramParticipationRepository, InterfaceForListener
{

    public function ofId(string $userId, string $programParticipationId): ProgramParticipation
    {
        $qb = $this->createQueryBuilder('programParticipation');
        $qb->select('programParticipation')
                ->andWhere($qb->expr()->eq('programParticipation.active', 'true'))
                ->andWhere($qb->expr()->eq('programParticipation.id', ':programParticipationId'))
                ->setParameter('programParticipationId', $programParticipationId)
                ->leftJoin('programParticipation.user', 'user')
                ->andWhere($qb->expr()->eq('user.id', ':userId'))
                ->setParameter('userId', $userId)
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
