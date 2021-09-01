<?php

namespace Query\Infrastructure\Persistence\Doctrine\Repository;

use Doctrine\ORM\EntityRepository;
use Doctrine\ORM\NoResultException;
use Query\Application\Service\Firm\Program\ParticipantRepository;
use Query\Domain\Model\Firm\Client\ClientParticipant;
use Query\Domain\Model\Firm\Program\Participant;
use Query\Domain\Model\Firm\Team\TeamProgramParticipation;
use Query\Domain\Model\User\UserParticipant;
use Query\Domain\Service\Firm\Program\ParticipantRepository as InterfaceForDomainService;
use Resources\Exception\RegularException;
use Resources\Infrastructure\Persistence\Doctrine\PaginatorBuilder;

class DoctrineParticipantRepository extends EntityRepository implements ParticipantRepository, InterfaceForDomainService
{

    public function all(
            string $firmId, string $programId, int $page, int $pageSize, ?bool $activeStatus, ?string $note, ?string $searchByName)
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
        
        if (isset($searchByName)) {
            $clientParticipantQb = $this->getEntityManager()->createQueryBuilder();
            $clientParticipantQb->select('a_participant.id')
                    ->from(ClientParticipant::class, 'clientParticipant')
                    ->leftJoin('clientParticipant.client', 'client')
                    ->orWhere($clientParticipantQb->expr()->like('client.personName.firstName', ":name"))
                    ->orWhere($clientParticipantQb->expr()->like('client.personName.lastName', ":name"))
                    ->leftJoin('clientParticipant.participant', 'a_participant');
            $userParticipantQb = $this->getEntityManager()->createQueryBuilder();
            
            $userParticipantQb = $this->getEntityManager()->createQueryBuilder();
            $userParticipantQb->select('b_participant.id')
                    ->from(UserParticipant::class, 'userParticipant')
                    ->leftJoin('userParticipant.user', 'user')
                    ->orWhere($userParticipantQb->expr()->like('user.personName.firstName', ":name"))
                    ->orWhere($userParticipantQb->expr()->like('user.personName.lastName', ":name"))
                    ->leftJoin('userParticipant.participant', 'b_participant');
            
            $teamParticipantQb = $this->getEntityManager()->createQueryBuilder();
            $teamParticipantQb->select('c_participant.id')
                    ->from(TeamProgramParticipation::class, 'teamParticipant')
                    ->leftJoin('teamParticipant.team', 'team')
                    ->orWhere($teamParticipantQb->expr()->like('team.name', ":name"))
                    ->leftJoin('teamParticipant.programParticipation', 'c_participant');
            
            $qb->andWhere($qb->expr()->orX(
                    $qb->expr()->in('participant.id', $clientParticipantQb->getDQL()),
                    $qb->expr()->in('participant.id', $userParticipantQb->getDQL()),
                    $qb->expr()->in('participant.id', $teamParticipantQb->getDQL())
            ))
                    ->setParameter('name', "%$searchByName%");
        }

        
        if (isset($activeStatus)) {
            $qb->andWhere($qb->expr()->eq("participant.active", ":activeStatus"))
                    ->setParameter("activeStatus", $activeStatus);
        }
        if (isset($note)) {
            $qb->andWhere($qb->expr()->eq("participant.note", ":note"))
                    ->setParameter("note", $note);
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

    public function containRecordOfParticipantInFirmCorrespondWithUser(string $firmId, string $userId): bool
    {
        $params = [
            'firmId' => $firmId,
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
                ->leftJoin('program.firm', 'firm')
                ->andWhere($qb->expr()->eq('firm.id', ':firmId'))
                ->setParameters($params)
                ->setMaxResults(1);

        return !empty($qb->getQuery()->getResult());
    }

}
