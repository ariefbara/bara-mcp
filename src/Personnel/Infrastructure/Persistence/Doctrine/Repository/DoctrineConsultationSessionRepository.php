<?php

namespace Personnel\Infrastructure\Persistence\Doctrine\Repository;

use Doctrine\ORM\ {
    EntityRepository,
    NoResultException
};
use Personnel\ {
    Application\Service\Firm\Personnel\ProgramConsultant\ConsultationSessionRepository,
    Application\Service\Firm\Personnel\ProgramConsultant\ProgramConsultantCompositionId,
    Domain\Model\Firm\Personnel\ProgramConsultant\ConsultationSession
};
use Resources\ {
    Exception\RegularException,
    Infrastructure\Persistence\Doctrine\PaginatorBuilder
};

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

    public function all(ProgramConsultantCompositionId $programConsultantCompositionId, int $page, int $pageSize)
    {
        $parameters = [
            "programConsultantId" => $programConsultantCompositionId->getProgramConsultantId(),
            "personnelId" => $programConsultantCompositionId->getPersonnelId(),
            "firmId" => $programConsultantCompositionId->getFirmId(),
        ];
        
        $qb = $this->createQueryBuilder("consultationSession");
        $qb->select('consultationSession')
                ->leftJoin('consultationSession.programConsultant', 'programConsultant')
                ->andWhere($qb->expr()->eq('programConsultant.removed', 'false'))
                ->andWhere($qb->expr()->eq('programConsultant.id', ':programConsultantId'))
                ->leftJoin('programConsultant.personnel', 'personnel')
                ->andWhere($qb->expr()->eq('personnel.removed', 'false'))
                ->andWhere($qb->expr()->eq('personnel.id', ':personnelId'))
                ->leftJoin('personnel.firm', 'firm')
                ->andWhere($qb->expr()->eq('firm.id', ':firmId'))
                ->setParameters($parameters);
        
        return PaginatorBuilder::build($qb->getQuery(), $page, $pageSize);
        
    }

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

}
