<?php

namespace Participant\Infrastructure\Persistence\Doctrine\Repository;

use Doctrine\ORM\ {
    EntityRepository,
    NoResultException
};
use Participant\ {
    Application\Service\Firm\Program\ConsultantRepository,
    Domain\DependencyModel\Firm\Program\Consultant,
    Domain\Model\ClientParticipant,
    Domain\Model\UserParticipant
};
use Resources\Exception\RegularException;

class DoctrineConsultantRepository extends EntityRepository implements ConsultantRepository
{

    public function aConsultantInProgramWhereClientParticipate(string $firmId, string $clientId,
            string $programParticipationId, string $consultantId): Consultant
    {
        $params = [
            'firmId' => $firmId,
            'clientId' => $clientId,
            'programParticipationId' => $programParticipationId,
            'consultantId' => $consultantId,
        ];

        $clientParticipantQb = $this->getEntityManager()->createQueryBuilder();
        $clientParticipantQb->select('t_program.id')
                ->from(ClientParticipant::class, 'clientParticipant')
                ->andWhere($clientParticipantQb->expr()->eq('clientParticipant.id', ':programParticipationId'))
                ->leftJoin('clientParticipant.client', 'client')
                ->andWhere($clientParticipantQb->expr()->eq('client.id', ':clientId'))
                ->andWhere($clientParticipantQb->expr()->eq('client.firmId', ':firmId'))
                ->leftJoin('clientParticipant.participant', 'participant')
                ->leftJoin('participant.program', 't_program')
                ->setMaxResults(1);

        $qb = $this->createQueryBuilder('consultant');
        $qb->select('consultant')
                ->andWhere($qb->expr()->eq('consultant.id', ':consultantId'))
                ->leftJoin("consultant.program", "program")
                ->andWhere($qb->expr()->in('program.id', $clientParticipantQb->getDQL()))
                ->setParameters($params)
                ->setMaxResults(1);

        try {
            return $qb->getQuery()->getSingleResult();
        } catch (NoResultException $ex) {
            $errorDetail = 'not found: consultant not found';
            throw RegularException::notFound($errorDetail);
        }
    }

    public function aConsultantInProgramWhereUserParticipate(string $userId, string $userParticipantId,
            string $consultantId): Consultant
    {
        $params = [
            'userId' => $userId,
            'userParticipantId' => $userParticipantId,
            'consultantId' => $consultantId,
        ];

        $userParticipantQb = $this->getEntityManager()->createQueryBuilder();
        $userParticipantQb->select('t_program.id')
                ->from(UserParticipant::class, 'userParticipant')
                ->andWhere($userParticipantQb->expr()->eq('userParticipant.userId', ':userId'))
                ->andWhere($userParticipantQb->expr()->eq('userParticipant.id', ':userParticipantId'))
                ->leftJoin('userParticipant.participant', 'participant')
                ->leftJoin('participant.program', 't_program')
                ->setMaxResults(1);

        $qb = $this->createQueryBuilder('consultant');
        $qb->select('consultant')
                ->andWhere($qb->expr()->eq('consultant.id', ':consultantId'))
                ->leftJoin("consultant.program", "program")
                ->andWhere($qb->expr()->in('program.id', $userParticipantQb->getDQL()))
                ->setParameters($params)
                ->setMaxResults(1);

        try {
            return $qb->getQuery()->getSingleResult();
        } catch (NoResultException $ex) {
            $errorDetail = 'not found: consultant not found';
            throw RegularException::notFound($errorDetail);
        }
    }

    public function ofId(string $consultantId): Consultant
    {
        $params = [
            "consultantId" => $consultantId,
        ];
        $qb = $this->createQueryBuilder("consultant");
        $qb->select("consultant")
                ->andWhere($qb->expr()->eq("consultant.id", ":consultantId"))
                ->setParameters($params)
                ->setMaxResults(1);

        try {
            return $qb->getQuery()->getSingleResult();
        } catch (NoResultException $ex) {
            $errorDetail = "not found: consultant not found";
            throw RegularException::notFound($errorDetail);
        }
    }

}
