<?php

namespace Personnel\Infrastructure\Persistence\Doctrine\Repository;

use Doctrine\ORM\ {
    EntityRepository,
    NoResultException
};
use Personnel\ {
    Application\Service\Firm\Personnel\ProgramConsultant\ConsultationRequestRepository,
    Domain\Model\Firm\Personnel\ProgramConsultant\ConsultationRequest
};
use Resources\Exception\RegularException;

class DoctrineConsultationRequestRepository extends EntityRepository implements ConsultationRequestRepository
{
    
    public function aConsultationRequestById(string $consultationRequestId): ConsultationRequest
    {
        $parameters = [
            "consultationRequestId" => $consultationRequestId,
        ];
        
        $qb = $this->createQueryBuilder('consultationRequest');
        $qb->select('consultationRequest')
                ->andWhere($qb->expr()->eq("consultationRequest.id", ":consultationRequestId"))
                ->setParameters($parameters)
                ->setMaxResults(1);
        
        try {
            return $qb->getQuery()->getSingleResult();
        } catch (NoResultException $ex) {
            $errorDetail = "not found: consultation request not found";
            throw RegularException::notFound($errorDetail);
        }
    }

}
