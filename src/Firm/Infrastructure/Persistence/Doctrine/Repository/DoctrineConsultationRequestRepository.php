<?php

namespace Firm\Infrastructure\Persistence\Doctrine\Repository;

use Doctrine\ORM\ {
    EntityRepository,
    NoResultException
};
use Firm\ {
    Application\Service\Firm\Program\ConsultationSetup\ConsultationRequestRepository,
    Domain\Model\Firm\Program\ClientParticipant,
    Domain\Model\Firm\Program\ConsultationSetup\ConsultationRequest
};
use Resources\Exception\RegularException;

class DoctrineConsultationRequestRepository extends EntityRepository implements ConsultationRequestRepository
{

    public function aConsultationRequestOfClient(
            string $firmId, string $clientId, string $programId, string $consultationRequestId): ConsultationRequest
    {
        $params = [
            'firmId' => $firmId,
            'clientId' => $clientId,
            'programId' => $programId,
            'consultationRequestId' => $consultationRequestId,
        ];
        
        $clientParticipantQb = $this->getEntityManager()->createQueryBuilder();
        $clientParticipantQb->select('tParticipant.id')
                ->from(ClientParticipant::class, 'clientParticipant')
                ->leftJoin('clientParticipant.participant', 'tParticipant')
                ->leftJoin('clientParticipant.client', 'client')
                ->andWhere($clientParticipantQb->expr()->eq('client.id', ':clientId'))
                ->leftJoin('clientParticipant.program', 'program')
                ->andWhere($clientParticipantQb->expr()->eq('program.id', ':programId'))
                ->leftJoin('client.firm', 'cFirm')
                ->andWhere($clientParticipantQb->expr()->eq('cFirm.id', ':firmId'))
                ->leftJoin('program.firm', 'pFirm')
                ->andWhere($clientParticipantQb->expr()->eq('pFirm.id', ':firmId'))
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

}
