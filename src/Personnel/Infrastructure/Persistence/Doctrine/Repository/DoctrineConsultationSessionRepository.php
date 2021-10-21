<?php

namespace Personnel\Infrastructure\Persistence\Doctrine\Repository;

use Doctrine\ORM\NoResultException;
use Personnel\Application\Service\Firm\Personnel\ProgramConsultant\ConsultationSessionRepository;
use Personnel\Domain\Model\Firm\Personnel\ProgramConsultant\ConsultationSession;
use Personnel\Domain\Task\Dependency\Firm\Personnel\Mentor\ConsultationSessionRepository as ConsultationSessionRepository2;
use Resources\Exception\RegularException;
use Resources\Infrastructure\Persistence\Doctrine\Repository\DoctrineEntityRepository;

class DoctrineConsultationSessionRepository extends DoctrineEntityRepository implements ConsultationSessionRepository, ConsultationSessionRepository2
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
                ->andWhere($qb->expr()->eq('programConsultant.active', 'true'))
                ->andWhere($qb->expr()->eq('programConsultant.id', ':programConsultantId'))
                ->leftJoin('programConsultant.personnel', 'personnel')
                ->andWhere($qb->expr()->eq('personnel.active', 'true'))
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

    public function aConsultationSessionOfId(string $id): ConsultationSession
    {
        return $this->findOneByIdOrDie($id, 'consultation session');
    }

    public function add(ConsultationSession $consultationSession): void
    {
        $this->persist($consultationSession);
    }

}
