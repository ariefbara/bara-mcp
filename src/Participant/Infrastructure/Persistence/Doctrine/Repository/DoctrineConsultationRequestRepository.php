<?php

namespace Participant\Infrastructure\Persistence\Doctrine\Repository;

use Doctrine\ORM\ {
    EntityRepository,
    NoResultException
};
use Participant\ {
    Application\Service\Participant\ConsultationRequestRepository,
    Domain\Model\ClientParticipant,
    Domain\Model\Participant\ConsultationRequest,
    Domain\Model\UserParticipant
};
use Resources\ {
    Exception\RegularException,
    Uuid
};

class DoctrineConsultationRequestRepository extends EntityRepository implements ConsultationRequestRepository
{

    public function aConsultationRequestFromClientParticipant(
            string $firmId, string $clientId, string $programParticipationId, string $consultationRequestId): ConsultationRequest
    {
        $params = [
            'firmId' => $firmId,
            'clientId' => $clientId,
            'programParticipationId' => $programParticipationId,
            'consultationRequestId' => $consultationRequestId,
        ];

        $clientParticipantQb = $this->getEntityManager()->createQueryBuilder();
        $clientParticipantQb->select('t_participant.id')
                ->from(ClientParticipant::class, 'clientParticipant')
                ->andWhere($clientParticipantQb->expr()->eq('clientParticipant.id', ':programParticipationId'))
                ->leftJoin('clientParticipant.participant', 't_participant')
                ->leftJoin('clientParticipant.client', 'client')
                ->andWhere($clientParticipantQb->expr()->eq('client.id', ':clientId'))
                ->andWhere($clientParticipantQb->expr()->eq('client.firmId', ':firmId'))
                ->setMaxResults(1);

        $qb = $this->createQueryBuilder('consultationRequest');
        $qb->select('consultationRequest')
                ->andWhere($qb->expr()->eq('consultationRequest.id', ':consultationRequestId'))
                ->leftJoin('consultationRequest.participant', 'participant')
                ->andWhere($qb->expr()->in('participant.id', $clientParticipantQb->getDQL()))
                ->setParameters($params)
                ->setMaxResults(1);

        try {
            return $qb->getQuery()->getSingleResult();
        } catch (NoResultException $ex) {
            $errorDetail = 'not found: consultation request not found';
            throw RegularException::notFound($errorDetail);
        }
    }

    public function aConsultationRequestFromUserParticipant(
            $userId, string $programParticipationId, string $consultationRequestId): ConsultationRequest
    {
        $params = [
            'userId' => $userId,
            'programParticipationId' => $programParticipationId,
            'consultationRequestId' => $consultationRequestId,
        ];

        $userParticipantQb = $this->getEntityManager()->createQueryBuilder();
        $userParticipantQb->select('tParticipant.id')
                ->from(UserParticipant::class, 'userParticipant')
                ->andWhere($userParticipantQb->expr()->eq('userParticipant.id', ':programParticipationId'))
                ->andWhere($userParticipantQb->expr()->eq('userParticipant.userId', ':userId'))
                ->leftJoin('userParticipant.participant', 'tParticipant')
                ->setMaxResults(1);

        $qb = $this->createQueryBuilder('consultationRequest');
        $qb->select('consultationRequest')
                ->andWhere($qb->expr()->eq('consultationRequest.id', ':consultationRequestId'))
                ->leftJoin('consultationRequest.participant', 'participant')
                ->andWhere($qb->expr()->in('participant.id', $userParticipantQb->getDQL()))
                ->setParameters($params)
                ->setMaxResults(1);

        try {
            return $qb->getQuery()->getSingleResult();
        } catch (NoResultException $ex) {
            $errorDetail = 'not found: consultation request not found';
            throw RegularException::notFound($errorDetail);
        }
    }

    public function add(ConsultationRequest $consultationRequest): void
    {
        $em = $this->getEntityManager();
        $em->persist($consultationRequest);
        $em->flush();
    }

    public function nextIdentity(): string
    {
        return Uuid::generateUuid4();
    }

    public function update(): void
    {
        $this->getEntityManager()->flush();
    }

}
