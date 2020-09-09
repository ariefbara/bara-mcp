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

    public function ofId(
            string $firmId, string $personnelId, string $programConsultationId, string $consultationRequestId): ConsultationRequest
    {
        $parameters = [
            "firmId" => $firmId,
            "personnelId" => $personnelId,
            "programConsultantId" => $programConsultationId,
            "consultationRequestId" => $consultationRequestId,
        ];

        $qb = $this->createQueryBuilder('consultationRequest');
        $qb->select('consultationRequest')
                ->andWhere($qb->expr()->eq('consultationRequest.id', ':consultationRequestId'))
                ->leftJoin("consultationRequest.programConsultant", "programConsultant")
                ->andWhere($qb->expr()->eq('programConsultant.id', ':programConsultantId'))
                ->leftJoin("programConsultant.personnel", "personnel")
                ->andWhere($qb->expr()->eq('personnel.id', ':personnelId'))
                ->andWhere($qb->expr()->eq('personnel.firmId', ':firmId'))
                ->setParameters($parameters)
                ->setMaxResults(1);

        try {
            return $qb->getQuery()->getSingleResult();
        } catch (NoResultException $ex) {
            $errorDetail = "not found: consultation request not found";
            throw RegularException::notFound($errorDetail);
        }
    }

    public function update(): void
    {
        $this->getEntityManager()->flush();
    }

}
