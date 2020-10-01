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
        $clientParticipantQb->select('participant.programId')
                ->from(ClientParticipant::class, 'clientParticipant')
                ->andWhere($clientParticipantQb->expr()->eq('clientParticipant.id', ':programParticipationId'))
                ->leftJoin('clientParticipant.participant', 'participant')
                ->leftJoin('clientParticipant.client', 'client')
                ->andWhere($clientParticipantQb->expr()->eq('client.id', ':clientId'))
                ->andWhere($clientParticipantQb->expr()->eq('client.firmId', ':firmId'))
                ->setMaxResults(1);

        $qb = $this->createQueryBuilder('consultant');
        $qb->select('consultant')
                ->andWhere($qb->expr()->eq('consultant.id', ':consultantId'))
                ->andWhere($qb->expr()->in('consultant.programId', $clientParticipantQb->getDQL()))
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
        $userParticipantQb->select('participant.programId')
                ->from(UserParticipant::class, 'userParticipant')
                ->andWhere($userParticipantQb->expr()->eq('userParticipant.userId', ':userId'))
                ->andWhere($userParticipantQb->expr()->eq('userParticipant.id', ':userParticipantId'))
                ->leftJoin('userParticipant.participant', 'participant')
                ->setMaxResults(1);

        $qb = $this->createQueryBuilder('consultant');
        $qb->select('consultant')
                ->andWhere($qb->expr()->eq('consultant.id', ':consultantId'))
                ->andWhere($qb->expr()->in('consultant.programId', $userParticipantQb->getDQL()))
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
