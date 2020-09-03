<?php

namespace Participant\Infrastructure\Persistence\Doctrine\Repository;

use Doctrine\ORM\ {
    EntityRepository,
    NoResultException
};
use Participant\ {
    Application\Service\Participant\ConsultationSessionRepository,
    Domain\Model\ClientParticipant,
    Domain\Model\Participant\ConsultationSession,
    Domain\Model\UserParticipant
};
use Resources\Exception\RegularException;

class DoctrineConsultationSessionRepository extends EntityRepository implements ConsultationSessionRepository
{

    public function aConsultationSessionOfClientParticipant(string $firmId, string $clientId,
            string $programParticipationId, string $consultationSessionId): ConsultationSession
    {
        $params = [
            'firmId' => $firmId,
            'clientId' => $clientId,
            'programParticipationId' => $programParticipationId,
            'consultationSessionId' => $consultationSessionId,
        ];

        $clientParticipantQb = $this->getEntityManager()->createQueryBuilder();
        $clientParticipantQb->select('tParticipant.id')
                ->from(ClientParticipant::class, 'clientParticipant')
                ->andWhere($clientParticipantQb->expr()->eq('clientParticipant.id', ':programParticipationId'))
                ->leftJoin('clientParticipant.participant', 'tParticipant')
                ->leftJoin('clientParticipant.client', 'client')
                ->andWhere($clientParticipantQb->expr()->eq('client.id', ':clientId'))
                ->andWhere($clientParticipantQb->expr()->eq('client.firmId', ':firmId'))
                ->setMaxResults(1);

        $qb = $this->createQueryBuilder('consultationSession');
        $qb->select('consultationSession')
                ->andWhere($qb->expr()->eq('consultationSession.id', ':consultationSessionId'))
                ->leftJoin('consultationSession.participant', 'participant')
                ->andWhere($qb->expr()->in('participant.id', $clientParticipantQb->getDQL()))
                ->setParameters($params)
                ->setMaxResults(1);

        try {
            return $qb->getQuery()->getSingleResult();
        } catch (NoResultException $ex) {
            $errorDetail = 'not found: consultation session not found';
            throw RegularException::notFound($errorDetail);
        }
    }

    public function aConsultationSessionOfUserParticipant(string $userId, string $programParticipationId,
            string $consultationSessionId): ConsultationSession
    {
        $params = [
            'userId' => $userId,
            'programParticipationId' => $programParticipationId,
            'consultationSessionId' => $consultationSessionId,
        ];

        $userParticipantQb = $this->getEntityManager()->createQueryBuilder();
        $userParticipantQb->select('tParticipant.id')
                ->from(UserParticipant::class, 'userParticipant')
                ->andWhere($userParticipantQb->expr()->eq('userParticipant.id', ':programParticipationId'))
                ->andWhere($userParticipantQb->expr()->eq('userParticipant.userId', ':userId'))
                ->leftJoin('userParticipant.participant', 'tParticipant')
                ->setMaxResults(1);

        $qb = $this->createQueryBuilder('consultationSession');
        $qb->select('consultationSession')
                ->andWhere($qb->expr()->eq('consultationSession.id', ':consultationSessionId'))
                ->leftJoin('consultationSession.participant', 'participant')
                ->andWhere($qb->expr()->in('participant.id', $userParticipantQb->getDQL()))
                ->setParameters($params)
                ->setMaxResults(1);

        try {
            return $qb->getQuery()->getSingleResult();
        } catch (NoResultException $ex) {
            $errorDetail = 'not found: consultation session not found';
            throw RegularException::notFound($errorDetail);
        }
    }

    public function update(): void
    {
        $this->getEntityManager()->flush();
    }

}
