<?php

namespace Query\Infrastructure\Persistence\Doctrine\Repository;

use Doctrine\ORM\ {
    EntityRepository,
    NoResultException
};
use Query\ {
    Application\Service\Firm\Personnel\ProgramConsultant\ConsultantCommentRepository,
    Domain\Model\Firm\Program\Consultant\ConsultantComment
};
use Resources\Exception\RegularException;

class DoctrineConsultantCommentRepository extends EntityRepository implements ConsultantCommentRepository
{
    
    public function ofId(string $firmId, string $personnelId, string $programConsultationId, string $consultantCommentId): ConsultantComment
    {
        $params = [
            'firmId' => $firmId,
            'personnelId' => $personnelId,
            'programConsultationId' => $programConsultationId,
            'consultantCommentId' => $consultantCommentId,
        ];
        
        $qb = $this->createQueryBuilder('consultantComment');
        $qb->select('consultantComment')
                ->andWhere($qb->expr()->eq('consultantComment.id', ':consultantCommentId'))
                ->leftJoin('consultantComment.consultant', 'programConsultation')
                ->andWhere($qb->expr()->eq('programConsultation.id', ':programConsultationId'))
                ->leftJoin('programConsultation.personnel', 'personnel')
                ->andWhere($qb->expr()->eq('personnel.id', ':personnelId'))
                ->leftJoin('personnel.firm', 'firm')
                ->andWhere($qb->expr()->eq('firm.id', ':firmId'))
                ->setParameters($params)
                ->setMaxResults(1);
        
        try {
            return $qb->getQuery()->getSingleResult();
        } catch (NoResultException $ex) {
            $errorDetail = 'not found: consultant comment not found';
            throw RegularException::notFound($errorDetail);
        }
    }

}
