<?php

namespace Query\Infrastructure\Persistence\Doctrine\Repository;

use Doctrine\ORM\ {
    EntityRepository,
    NoResultException
};
use Query\ {
    Application\Service\Firm\Program\ParticipantRepository,
    Domain\Model\Firm\Client\ClientParticipant,
    Domain\Model\Firm\Program\Participant,
    Domain\Model\Firm\Team\TeamProgramParticipation,
    Domain\Model\User\UserParticipant,
    Domain\Service\Firm\Program\ParticipantRepository as InterfaceForDomainService
};
use Resources\ {
    Exception\RegularException,
    Infrastructure\Persistence\Doctrine\PaginatorBuilder
};

class DoctrineParticipantRepository extends EntityRepository implements ParticipantRepository, InterfaceForDomainService
{

    public function all(string $firmId, string $programId, int $page, int $pageSize, ?bool $activeStatus)
    {
        $params = [
            "firmId" => $firmId,
            "programId" => $programId,
        ];

        $qb = $this->createQueryBuilder('participant');
        $qb->select('participant')
                ->leftJoin('participant.program', 'program')
                ->andWhere($qb->expr()->eq('program.id', ":programId"))
                ->leftJoin('program.firm', 'firm')
                ->andWhere($qb->expr()->eq('firm.id', ":firmId"))
                ->setParameters($params);
        
        if (isset($activeStatus)) {
            $qb->andWhere($qb->expr()->eq("participant.active", ":activeStatus"))
                    ->setParameter("activeStatus", $activeStatus);
        }

        return PaginatorBuilder::build($qb->getQuery(), $page, $pageSize);
    }

    public function ofId(string $firmId, string $programId, string $participantId): Participant
    {
        $params = [
            "firmId" => $firmId,
            "programId" => $programId,
            "participantId" => $participantId,
        ];

        $qb = $this->createQueryBuilder('participant');
        $qb->select('participant')
                ->andWhere($qb->expr()->eq('participant.id', ":participantId"))
                ->leftJoin('participant.program', 'program')
                ->andWhere($qb->expr()->eq('program.id', ":programId"))
                ->leftJoin('program.firm', 'firm')
                ->andWhere($qb->expr()->eq('firm.id', ":firmId"))
                ->setParameters($params)
                ->setMaxResults(1);

        try {
            return $qb->getQuery()->getSingleResult();
        } catch (NoResultException $ex) {
            $errorDetail = "not found: participant not found";
            throw RegularException::notFound($errorDetail);
        }
    }

    public function containRecordOfActiveParticipantCorrespondWithClient(string $firmId, string $programId,
            string $clientId): bool
    {
        $params = [
            'clientId' => $clientId,
            'programId' => $programId,
            'firmId' => $firmId,
        ];

        $clientParticipantQb = $this->getEntityManager()->createQueryBuilder();
        $clientParticipantQb->select('cp_participant.id')
                ->from(ClientParticipant::class, 'clientParticipant')
                ->leftJoin('clientParticipant.participant', 'cp_participant')
                ->leftJoin('clientParticipant.client', 'client')
                ->andWhere($clientParticipantQb->expr()->eq('client.id', ':clientId'))
                ->leftJoin('client.firm', 'cp_firm')
                ->andWhere($clientParticipantQb->expr()->eq('cp_firm.id', ':firmId'))
                ->setMaxResults(1);

        $qb = $this->createQueryBuilder('participant');
        $qb->select('1')
                ->andWhere($qb->expr()->eq('participant.active', 'true'))
                ->andWhere($qb->expr()->in('participant.id', $clientParticipantQb->getDQL()))
                ->leftJoin('participant.program', 'program')
                ->andWhere($qb->expr()->eq('program.id', ':programId'))
                ->leftJoin('program.firm', 'firm')
                ->andWhere($qb->expr()->eq('firm.id', ':firmId'))
                ->setParameters($params)
                ->setMaxResults(1);

        return !empty($qb->getQuery()->getResult());
    }

    public function containRecordOfActiveParticipantCorrespondWithUser(string $firmId, string $programId, string $userId): bool
    {
        $params = [
            'firmId' => $firmId,
            'programId' => $programId,
            'userId' => $userId,
        ];

        $participantQb = $this->getEntityManager()->createQueryBuilder();
        $participantQb->select('cp_participant.id')
                ->from(UserParticipant::class, 'userParticipant')
                ->leftJoin('userParticipant.participant', 'cp_participant')
                ->leftJoin('userParticipant.user', 'user')
                ->andWhere($participantQb->expr()->eq('user.id', ':userId'))
                ->setMaxResults(1);

        $qb = $this->createQueryBuilder('participant');
        $qb->select('1')
                ->andWhere($qb->expr()->eq('participant.active', 'true'))
                ->andWhere($qb->expr()->in('participant.id', $participantQb->getDQL()))
                ->leftJoin('participant.program', 'program')
                ->andWhere($qb->expr()->eq('program.id', ':programId'))
                ->leftJoin('program.firm', 'firm')
                ->andWhere($qb->expr()->eq('firm.id', ':firmId'))
                ->setParameters($params)
                ->setMaxResults(1);

        return !empty($qb->getQuery()->getResult());
    }

    public function aParticipantOfProgram(string $programId, string $participantId): Participant
    {
        $params = [
            "programId" => $programId,
            "participantId" => $participantId,
        ];

        $qb = $this->createQueryBuilder("participant");
        $qb->select("participant")
                ->andWhere($qb->expr()->eq("participant.id", ":participantId"))
                ->leftJoin("participant.program", "program")
                ->andWhere($qb->expr()->eq("program.id", ":programId"))
                ->setParameters($params)
                ->setMaxResults(1);

        try {
            return $qb->getQuery()->getSingleResult();
        } catch (NoResultException $ex) {
            $errorDetail = "not found: participant not found";
            throw RegularException::notFound($errorDetail);
        }
    }

    public function allParticipantsOfProgram(string $programId, int $page, int $pageSize, ?bool $activeStatus)
    {
        $params = [
            "programId" => $programId,
        ];

        $qb = $this->createQueryBuilder("participant");
        $qb->select("participant")
                ->leftJoin("participant.program", "program")
                ->andWhere($qb->expr()->eq("program.id", ":programId"))
                ->setParameters($params);
        
        if (isset($activeStatus)) {
            $qb->andWhere($qb->expr()->eq("participant.active", ":activeStatus"))
                    ->setParameter("activeStatus", $activeStatus);
        }

        return PaginatorBuilder::build($qb->getQuery(), $page, $pageSize);
    }

    public function containRecordOfActiveParticipantCorrespondWithTeam(string $teamId, string $programId): bool
    {
        $params = [
            'teamId' => $teamId,
            'programId' => $programId,
        ];

        $participantQb = $this->getEntityManager()->createQueryBuilder();
        $participantQb->select('tp_participant.id')
                ->from(TeamProgramParticipation::class, 'teamParticipant')
                ->leftJoin('teamParticipant.programParticipation', 'tp_participant')
                ->leftJoin('teamParticipant.team', 'team')
                ->andWhere($participantQb->expr()->eq('team.id', ':teamId'))
                ->setMaxResults(1);

        $qb = $this->createQueryBuilder('participant');
        $qb->select('1')
                ->andWhere($qb->expr()->eq('participant.active', 'true'))
                ->andWhere($qb->expr()->in('participant.id', $participantQb->getDQL()))
                ->leftJoin('participant.program', 'program')
                ->andWhere($qb->expr()->eq('program.id', ':programId'))
                ->setParameters($params)
                ->setMaxResults(1);

        return !empty($qb->getQuery()->getResult());
    }

}
