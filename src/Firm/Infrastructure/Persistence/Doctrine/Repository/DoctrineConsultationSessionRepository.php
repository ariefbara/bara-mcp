<?php

namespace Firm\Infrastructure\Persistence\Doctrine\Repository;

use Doctrine\ORM\ {
    EntityRepository,
    NoResultException
};
use Firm\ {
    Application\Service\Firm\Program\ConsultationSetup\ConsultationSessionRepository,
    Domain\Model\Firm\Program\ClientParticipant,
    Domain\Model\Firm\Program\ConsultationSetup\ConsultationSession
};
use Resources\Exception\RegularException;

class DoctrineConsultationSessionRepository extends EntityRepository implements ConsultationSessionRepository
{

    public function aConsultationSessionOfClient(
            string $firmId, string $clientId, string $programId, string $consultationSessionId): ConsultationSession
    {
        $params = [
            'firmId' => $firmId,
            'clientId' => $clientId,
            'programId' => $programId,
            'consultationSessionId' => $consultationSessionId,
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

}
