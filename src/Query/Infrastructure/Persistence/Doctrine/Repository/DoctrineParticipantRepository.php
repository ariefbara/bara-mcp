<?php

namespace Query\Infrastructure\Persistence\Doctrine\Repository;

use Doctrine\ORM\ {
    EntityRepository,
    NoResultException
};
use Firm\Application\Service\Firm\Program\ProgramCompositionId;
use Query\ {
    Application\Service\Client\ProgramParticipationRepository,
    Application\Service\Firm\Program\ParticipantRepository,
    Domain\Model\Firm\Program\Participant
};
use Resources\ {
    Exception\RegularException,
    Infrastructure\Persistence\Doctrine\PaginatorBuilder
};

class DoctrineParticipantRepository extends EntityRepository implements ParticipantRepository, ProgramParticipationRepository
{

    public function all(ProgramCompositionId $programCompositionId, int $page, int $pageSize)
    {
        $parameters = [
            "programId" => $programCompositionId->getProgramId(),
            "firmId" => $programCompositionId->getFirmId(),
        ];

        $qb = $this->createQueryBuilder('participant');
        $qb->select('participant')
                ->leftJoin('participant.program', 'program')
                ->andWhere($qb->expr()->eq('program.removed', "false"))
                ->andWhere($qb->expr()->eq('program.id', ":programId"))
                ->leftJoin('program.firm', 'firm')
                ->andWhere($qb->expr()->eq('firm.id', ":firmId"))
                ->setParameters($parameters);

        return PaginatorBuilder::build($qb->getQuery(), $page, $pageSize);
    }

    public function ofId(ProgramCompositionId $programCompositionId, string $participantId): Participant
    {
        $parameters = [
            "participantId" => $participantId,
            "programId" => $programCompositionId->getProgramId(),
            "firmId" => $programCompositionId->getFirmId(),
        ];

        $qb = $this->createQueryBuilder('participant');
        $qb->select('participant')
                ->andWhere($qb->expr()->eq('participant.id', ":participantId"))
                ->leftJoin('participant.program', 'program')
                ->andWhere($qb->expr()->eq('program.removed', "false"))
                ->andWhere($qb->expr()->eq('program.id', ":programId"))
                ->leftJoin('program.firm', 'firm')
                ->andWhere($qb->expr()->eq('firm.id', ":firmId"))
                ->setParameters($parameters)
                ->setMaxResults(1);

        try {
            return $qb->getQuery()->getSingleResult();
        } catch (NoResultException $ex) {
            $errorDetail = "not found: participant not found";
            throw RegularException::notFound($errorDetail);
        }
    }

    public function aProgramParticipationOfClient(string $clientId, string $programParticipationId): Participant
    {
        $parameters = [
            "participantId" => $programParticipationId,
            "clientId" => $clientId,
        ];

        $qb = $this->createQueryBuilder('participant');
        $qb->select('participant')
                ->andWhere($qb->expr()->eq('participant.id', ":participantId"))
                ->leftJoin('participant.client', 'client')
                ->andWhere($qb->expr()->eq('client.id', ":clientId"))
                ->setParameters($parameters)
                ->setMaxResults(1);

        try {
            return $qb->getQuery()->getSingleResult();
        } catch (NoResultException $ex) {
            $errorDetail = "not found: participant not found";
            throw RegularException::notFound($errorDetail);
        }
    }

    public function allProgramParticipationsOfClient(string $clientId, int $page, int $pageSize)
    {
        $parameters = [
            "clientId" => $clientId,
        ];

        $qb = $this->createQueryBuilder('participant');
        $qb->select('participant')
                ->leftJoin('participant.client', 'client')
                ->andWhere($qb->expr()->eq('client.id', ":clientId"))
                ->setParameters($parameters);

        return PaginatorBuilder::build($qb->getQuery(), $page, $pageSize);
    }

}
