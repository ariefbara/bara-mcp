<?php

namespace Participant\Infrastructure\Persistence\Doctrine\Repository;

use Doctrine\ORM\ {
    EntityRepository,
    NoResultException
};
use Participant\ {
    Application\Service\Participant\ConsultationRequestRepository,
    Domain\Model\Participant\ConsultationRequest
};
use Query\Domain\Model\Firm\Program\ {
    ClientParticipant,
    UserParticipant
};
use Resources\ {
    Exception\RegularException,
    Uuid
};

class DoctrineConsultationRequestRepository extends EntityRepository implements ConsultationRequestRepository
{

    public function add(ConsultationRequest $consultationRequest): void
    {
        $em = $this->getEntityManager();
        $em->persist($consultationRequest);
        $em->flush();
    }

    public function consultationRequestFromClient(string $firmId, string $clientId, string $programId,
            string $consultationRequestId): ConsultationRequest
    {
        $params = [
            'firmId' => $firmId,
            'clientId' => $clientId,
            'programId' => $programId,
            'consultationRequestId' => $consultationRequestId,
        ];

        $clientParticipantQb = $this->getEntityManager()->createQueryBuilder();
        $clientParticipantQb->select('tParticipant.id')
                ->from(ClientParticipant::class, 'tClientParticipant')
                ->leftJoin('tClientParticipant.participant', 'tParticipant')
                ->leftJoin('tClientParticipant.program', 'tProgram')
                ->andWhere($clientParticipantQb->expr()->eq('tProgram.id', ':programId'))
                ->leftJoin('tClientParticipant.client', 'tClient')
                ->andWhere($clientParticipantQb->expr()->eq('tClient.id', ':clientId'))
                ->leftJoin('tClient.firm', 'tFirm')
                ->andWhere($clientParticipantQb->expr()->eq('tFirm.id', ':firmId'))
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

    public function consultationRequestFromUser(string $userId, string $firmId, string $programId,
            string $consultationRequestId): ConsultationRequest
    {
        $params = [
            'userId' => $userId,
            'firmId' => $firmId,
            'programId' => $programId,
            'consultationRequestId' => $consultationRequestId,
        ];

        $userParticipantQb = $this->getEntityManager()->createQueryBuilder();
        $userParticipantQb->select('tParticipant.id')
                ->from(UserParticipant::class, 'tUserParticipant')
                ->leftJoin('tUserParticipant.participant', 'tParticipant')
                ->leftJoin('tUserParticipant.user', 'tUser')
                ->andWhere($userParticipantQb->expr()->eq('tUser.id', ':userId'))
                ->leftJoin('tUserParticipant.program', 'tProgram')
                ->andWhere($userParticipantQb->expr()->eq('tProgram.id', ':programId'))
                ->leftJoin('tProgram.firm', 'tFirm')
                ->andWhere($userParticipantQb->expr()->eq('tFirm.id', ':firmId'))
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

    public function nextIdentity(): string
    {
        return Uuid::generateUuid4();
    }

    public function update(): void
    {
        $this->getEntityManager()->flush();
    }

}
