<?php

namespace Personnel\Infrastructure\Persistence\Doctrine\Repository;

use Doctrine\ORM\ {
    EntityRepository,
    NoResultException
};
use Personnel\ {
    Application\Service\Firm\Personnel\ProgramConsultant\ConsultationRequestRepository,
    Application\Service\Firm\Personnel\ProgramConsultant\ProgramConsultantCompositionId,
    Domain\Model\Firm\Personnel\ProgramConsultant\ConsultationRequest
};
use Resources\ {
    Exception\RegularException,
    Infrastructure\Persistence\Doctrine\PaginatorBuilder
};

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

    public function all(ProgramConsultantCompositionId $programConsultantCompositionId, int $page, int $pageSize)
    {
        $parameters = [
            "programConsultantId" => $programConsultantCompositionId->getProgramConsultantId(),
            "personnelId" => $programConsultantCompositionId->getPersonnelId(),
            "firmId" => $programConsultantCompositionId->getFirmId(),
        ];
        
        $qb = $this->createQueryBuilder('consultationRequest');
        $qb->select('consultationRequest')
                ->leftJoin("consultationRequest.programConsultant", "programConsultant")
                ->andWhere($qb->expr()->eq('programConsultant.removed', 'false'))
                ->andWhere($qb->expr()->eq('programConsultant.id', ':programConsultantId'))
                ->leftJoin("programConsultant.personnel", "personnel")
                ->andWhere($qb->expr()->eq('personnel.removed', 'false'))
                ->andWhere($qb->expr()->eq('personnel.id', ':personnelId'))
                ->leftJoin("personnel.firm", "firm")
                ->andWhere($qb->expr()->eq('firm.id', ':firmId'))
                ->setParameters($parameters);
        
        return PaginatorBuilder::build($qb->getQuery(), $page, $pageSize);
        
    }

    public function ofId(ProgramConsultantCompositionId $programConsultantCompositionId, string $consultationRequestId): ConsultationRequest
    {
        $parameters = [
            "consultationRequestId" => $consultationRequestId,
            "programConsultantId" => $programConsultantCompositionId->getProgramConsultantId(),
            "personnelId" => $programConsultantCompositionId->getPersonnelId(),
            "firmId" => $programConsultantCompositionId->getFirmId(),
        ];
        
        $qb = $this->createQueryBuilder('consultationRequest');
        $qb->select('consultationRequest')
                ->andWhere($qb->expr()->eq('consultationRequest.id', ':consultationRequestId'))
                ->leftJoin("consultationRequest.programConsultant", "programConsultant")
                ->andWhere($qb->expr()->eq('programConsultant.removed', 'false'))
                ->andWhere($qb->expr()->eq('programConsultant.id', ':programConsultantId'))
                ->leftJoin("programConsultant.personnel", "personnel")
                ->andWhere($qb->expr()->eq('personnel.removed', 'false'))
                ->andWhere($qb->expr()->eq('personnel.id', ':personnelId'))
                ->leftJoin("personnel.firm", "firm")
                ->andWhere($qb->expr()->eq('firm.id', ':firmId'))
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
