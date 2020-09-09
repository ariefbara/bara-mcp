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
    public function ofId(string $firmId, string $personnelId, string $programConsultationId,
            string $consultationSessionId): ConsultationSession
    {
        $parameters = [
            "firmId" => $firmId,
            "personnelId" => $personnelId,
            "programConsultantId" => $programConsultationId,
            "consultationSessionId" => $consultationSessionId,
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
                ->andWhere($qb->expr()->eq('personnel.firmId', ':firmId'))
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
