<?php

namespace Personnel\Infrastructure\Persistence\Doctrine\Repository;

use Doctrine\ORM\ {
    EntityRepository,
    NoResultException
};
use Personnel\ {
    Application\Service\Firm\PersonnelRepository,
    Domain\Model\Firm\Personnel,
    Domain\Model\Firm\Personnel\ProgramConsultant\ConsultationRequest,
    Domain\Model\Firm\Personnel\ProgramConsultant\ConsultationSession
};
use Resources\Exception\RegularException;

class DoctrinePersonnelRepository extends EntityRepository implements PersonnelRepository
{

    public function aPersonnelHavingConsultationRequest(string $consultationRequestId): Personnel
    {
        $parameters = [
            "consultationRequestId" => $consultationRequestId,
        ];
        
        $subQuery = $this->getEntityManager()->createQueryBuilder();
        $subQuery->select('tPersonnel.id')
                ->from(ConsultationRequest::class, 'consultationRequest')
                ->andWhere($subQuery->expr()->eq('consultationRequest.id', ":consultationRequestId"))
                ->leftJoin("consultationRequest.programConsultant", "programConsultant")
                ->leftJoin("programConsultant.personnel", "tPersonnel")
                ->setMaxResults(1);
        
        $qb = $this->createQueryBuilder('personnel');
        $qb->select('personnel')
                ->andWhere($qb->expr()->in("personnel.id", $subQuery->getDQL()))
                ->setParameters($parameters)
                ->setMaxResults(1);
        try {
            return $qb->getQuery()->getSingleResult();
        } catch (NoResultException $ex) {
            $errorDetail = "not found: personnel not found";
            throw RegularException::notFound($errorDetail);
        }
    }

    public function aPersonnelHavingConsultationSession(string $consultationSessionId): Personnel
    {
        $parameters = [
            "consultationSessionId" => $consultationSessionId,
        ];
        
        $subQuery = $this->getEntityManager()->createQueryBuilder();
        $subQuery->select('tPersonnel.id')
                ->from(ConsultationSession::class, 'consultationSession')
                ->andWhere($subQuery->expr()->eq('consultationSession.id', ":consultationSessionId"))
                ->leftJoin("consultationSession.programConsultant", "programConsultant")
                ->leftJoin("programConsultant.personnel", "tPersonnel")
                ->setMaxResults(1);
        
        $qb = $this->createQueryBuilder('personnel');
        $qb->select('personnel')
                ->andWhere($qb->expr()->in("personnel.id", $subQuery->getDQL()))
                ->setParameters($parameters)
                ->setMaxResults(1);
        try {
            return $qb->getQuery()->getSingleResult();
        } catch (NoResultException $ex) {
            $errorDetail = "not found: personnel not found";
            throw RegularException::notFound($errorDetail);
        }
    }

}
