<?php

namespace Personnel\Infrastructure\Persistence\Doctrine\Repository;

use Doctrine\ORM\ {
    EntityRepository,
    NoResultException
};
use Personnel\ {
    Application\Listener\ConsultationSessionRepository as InterfaceForListener,
    Application\Service\Firm\Personnel\ProgramConsultant\ConsultationSessionRepository,
    Application\Service\Firm\Personnel\ProgramConsultant\ProgramConsultantCompositionId,
    Domain\Model\Firm\Personnel\ProgramConsultant\ConsultationSession
};
use Resources\Exception\RegularException;

class DoctrineConsultationSessionRepository extends EntityRepository implements ConsultationSessionRepository, InterfaceForListener
{

    public function ofId(ProgramConsultantCompositionId $programConsultantCompositionId, string $consultationSessionId): ConsultationSession
    {
        $parameters = [
            "consultationSessionId" => $consultationSessionId,
            "programConsultantId" => $programConsultantCompositionId->getProgramConsultantId(),
            "personnelId" => $programConsultantCompositionId->getPersonnelId(),
            "firmId" => $programConsultantCompositionId->getFirmId(),
        ];
        
        $qb = $this->createQueryBuilder("consultationSession");
        $qb->select('consultationSession')
                ->andWhere($qb->expr()->eq('consultationSession.id', ':consultationSessionId'))
                ->leftJoin('consultationSession.programConsultant', 'programConsultant')
                ->andWhere($qb->expr()->eq('programConsultant.removed', 'false'))
                ->andWhere($qb->expr()->eq('programConsultant.id', ':programConsultantId'))
                ->leftJoin('programConsultant.personnel', 'personnel')
                ->andWhere($qb->expr()->eq('personnel.removed', 'false'))
                ->andWhere($qb->expr()->eq('personnel.id', ':personnelId'))
                ->leftJoin('personnel.firm', 'firm')
                ->andWhere($qb->expr()->eq('firm.id', ':firmId'))
                ->setParameters($parameters)
                ->setMaxResults(1);
        
        try {
            return $qb->getQuery()->getSingleResult();
        } catch (NoResultException $ex) {
            $errorDetail = "not found: consultation session not found";
            throw RegularException::notFound($errorDetail);
        }
    }

    public function update(): void
    {
        $this->getEntityManager()->flush();
    }

    public function aConsultationSessionOfParticipant(string $clientId, string $participantId,
            string $consultationSessionId): ConsultationSession
    {
        $params = [
            "consultationSessionId" => $consultationSessionId,
            "participantId" => $participantId,
            "clientId" => $clientId,
        ];
        
        $qb = $this->createQueryBuilder("consultationSession");
        $qb->select('consultationSession')
                ->andWhere($qb->expr()->eq('consultationSession.id', ':consultationSessionId'))
                ->leftJoin('consultationSession.participant', 'participant')
                ->andWhere($qb->expr()->eq('participant.active', 'true'))
                ->andWhere($qb->expr()->eq('participant.id', ':participantId'))
                ->leftJoin('participant.client', 'client')
                ->andWhere($qb->expr()->eq('client.id', ':clientId'))
                ->setParameters($params)
                ->setMaxResults(1);
        
        try {
            return $qb->getQuery()->getSingleResult();
        } catch (NoResultException $ex) {
            $errorDetail = "not found: consultation session not found";
            throw RegularException::notFound($errorDetail);
        }
    }

}
