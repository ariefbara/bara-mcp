<?php

namespace Personnel\Infrastructure\Persistence\Doctrine\Repository;

use Doctrine\ORM\ {
    EntityRepository,
    NoResultException
};
use Personnel\ {
    Application\Service\Firm\Personnel\ProgramConsultant\ConsultationSessionRepository,
    Domain\Model\Firm\Personnel\ProgramConsultant\ConsultationSession
};
use Resources\Exception\RegularException;

class DoctrineConsultationSessionRepository extends EntityRepository implements ConsultationSessionRepository
{
    
    public function aConsultationSessionById(string $consultationSessionId): ConsultationSession
    {
        $parameters = [
            "consultationSessionId" => $consultationSessionId,
        ];
        $qb = $this->createQueryBuilder('consultationSession');
        $qb->select("consultationSession")
                ->andWhere($qb->expr()->eq("consultationSession.id", ":consultationSessionId"))
                ->setParameters($parameters)
                ->setMaxResults(1);
        
        try {
            return $qb->getQuery()->getSingleResult();
        } catch (NoResultException $ex) {
            $errorDetail = "not found: consultation session not found";
            throw RegularException::notFound($errorDetail);
        }
    }

}
