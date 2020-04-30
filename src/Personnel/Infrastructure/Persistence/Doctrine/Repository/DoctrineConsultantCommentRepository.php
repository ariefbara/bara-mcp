<?php

namespace Personnel\Infrastructure\Persistence\Doctrine\Repository;

use Doctrine\ORM\ {
    EntityRepository,
    NoResultException
};
use Personnel\ {
    Application\Service\Firm\Personnel\ProgramConsultant\ConsultantCommentRepository,
    Application\Service\Firm\Personnel\ProgramConsultant\ProgramConsultantCompositionId,
    Domain\Model\Firm\Personnel\ProgramConsultant\ConsultantComment
};
use Resources\ {
    Exception\RegularException,
    Uuid
};

class DoctrineConsultantCommentRepository extends EntityRepository implements ConsultantCommentRepository
{
    
    public function add(ConsultantComment $consultantComment): void
    {
        $em = $this->getEntityManager();
        $em->persist($consultantComment);
        $em->flush();
    }

    public function nextIdentity(): string
    {
        return Uuid::generateUuid4();
    }

    public function ofId(ProgramConsultantCompositionId $programConsultantCompositionId, string $consultantCommentId): ConsultantComment
    {
        $parameters = [
            "consultantCommentId" => $consultantCommentId,
            "programConsultantId" => $programConsultantCompositionId->getProgramConsultantId(),
            "personnelId" => $programConsultantCompositionId->getPersonnelId(),
            "firmId" => $programConsultantCompositionId->getFirmId(),
        ];
        
        $qb = $this->createQueryBuilder('consultantComment');
        $qb->select('consultantComment')
                ->andWhere($qb->expr()->eq('consultantComment.id', ":consultantCommentId"))
                ->leftJoin('consultantComment.comment', 'comment')
                ->andWhere($qb->expr()->eq('comment.removed', "false"))
                ->leftJoin('consultantComment.programConsultant', 'programConsultant')
                ->andWhere($qb->expr()->eq('programConsultant.removed', "false"))
                ->andWhere($qb->expr()->eq('programConsultant.id', ":programConsultantId"))
                ->leftJoin('programConsultant.personnel', 'personnel')
                ->andWhere($qb->expr()->eq('personnel.removed', "false"))
                ->andWhere($qb->expr()->eq('personnel.id', ":personnelId"))
                ->leftJoin('personnel.firm', 'firm')
                ->andWhere($qb->expr()->eq('firm.id', ":firmId"))
                ->setParameters($parameters)
                ->setMaxResults(1);
        
        try {
            return $qb->getQuery()->getSingleResult();
        } catch (NoResultException $ex) {
            $errorDetail = "not found: consultant comment not found";
            throw RegularException::notFound($errorDetail);
        }
    }

    public function update(): void
    {
        $this->getEntityManager()->flush();
    }

}
