<?php

namespace Participant\Infrastructure\Persistence\Doctrine\Repository;

use Doctrine\ORM\ {
    EntityRepository,
    NoResultException
};
use Participant\ {
    Application\Service\Firm\Program\MissionRepository,
    Domain\DependencyModel\Firm\Program\Mission,
    Domain\Model\ClientParticipant,
    Domain\Model\UserParticipant
};
use Resources\Exception\RegularException;

class DoctrineMissionRepository extends EntityRepository implements MissionRepository
{

    public function aMissionInProgramWhereClientParticipate(
            string $firmId, string $clientId, string $programParticipationId, string $missionId): Mission
    {
        $params = [
            'firmId' => $firmId,
            'clientId' => $clientId,
            'programParticipationId' => $programParticipationId,
            'missionId' => $missionId,
        ];

        $clientParticipantQb = $this->getEntityManager()->createQueryBuilder();
        $clientParticipantQb->select('t_program.id')
                ->from(ClientParticipant::class, 'clientParticipant')
                ->andWhere($clientParticipantQb->expr()->eq('clientParticipant.id', ':programParticipationId'))
                ->leftJoin('clientParticipant.participant', 'participant')
                ->leftJoin('participant.program', 't_program')
                ->leftJoin('clientParticipant.client', 'client')
                ->andWhere($clientParticipantQb->expr()->eq('client.id', ':clientId'))
                ->andWhere($clientParticipantQb->expr()->eq('client.firmId', ':firmId'))
                ->setMaxResults(1);
        
        $qb = $this->createQueryBuilder('mission');
        $qb->select('mission')
                ->andWhere($qb->expr()->eq('mission.id', ':missionId'))
                ->leftJoin("mission.program", "program")
                ->andWhere($qb->expr()->in('program.id', $clientParticipantQb->getDQL()))
                ->setParameters($params)
                ->setMaxResults(1);

        try {
            return $qb->getQuery()->getSingleResult();
        } catch (NoResultException $ex) {
            $errorDetail = 'not found: mission not found';
            throw RegularException::notFound($errorDetail);
        }
    }

    public function aMissionInProgramWhereUserParticipate(
            string $userId, string $programParticipationId, string $missionId): Mission
    {
        $params = [
            'userId' => $userId,
            'programParticipationId' => $programParticipationId,
            'missionId' => $missionId,
        ];

        $userParticipantQb = $this->getEntityManager()->createQueryBuilder();
        $userParticipantQb->select('t_program.id')
                ->from(UserParticipant::class, 'userParticipant')
                ->andWhere($userParticipantQb->expr()->eq('userParticipant.userId', ':userId'))
                ->andWhere($userParticipantQb->expr()->eq('userParticipant.id', ':programParticipationId'))
                ->leftJoin('userParticipant.participant', 'participant')
                ->leftJoin('participant.program', 't_program')
                ->setMaxResults(1);
        
        $qb = $this->createQueryBuilder('mission');
        $qb->select('mission')
                ->andWhere($qb->expr()->eq('mission.id', ':missionId'))
                ->leftJoin("mission.program", "program")
                ->andWhere($qb->expr()->in('program.id', $userParticipantQb->getDQL()))
                ->setParameters($params)
                ->setMaxResults(1);

        try {
            return $qb->getQuery()->getSingleResult();
        } catch (NoResultException $ex) {
            $errorDetail = 'not found: mission not found';
            throw RegularException::notFound($errorDetail);
        }
    }

    public function ofId(string $missionId): Mission
    {
        $params = [
            "missionId" => $missionId,
        ];
        $qb = $this->createQueryBuilder("mission");
        $qb->select("mission")
                ->andWhere($qb->expr()->eq("mission.id", ":missionId"))
                ->setParameters($params)
                ->setMaxResults(1);
        
        try {
            return $qb->getQuery()->getSingleResult();
        } catch (NoResultException $ex) {
            $errorDetail = "not found: mission not found";
            throw RegularException::notFound($errorDetail);
        }
    }

}
