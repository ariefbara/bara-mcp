<?php

namespace Query\Infrastructure\Persistence\Doctrine\Repository;

use Doctrine\ORM\ {
    EntityRepository,
    NoResultException
};
use Personnel\Application\Service\Firm\Personnel\ProgramConsultant\ProgramConsultantCompositionId;
use Query\ {
    Application\Service\Firm\Personnel\ProgramConsultant\ConsultantCommentRepository,
    Domain\Model\Firm\Program\Consultant\ConsultantComment
};
use Resources\Exception\RegularException;

class DoctrineConsultantCommentRepository extends EntityRepository implements ConsultantCommentRepository
{

    public function aCommentFromProgramConsultant(
            ProgramConsultantCompositionId $programConsultantCompositionId, string $consultantCommentId): ConsultantComment
    {
        $params = [
            'consultantCommentId' => $consultantCommentId,
            'consultantId' => $programConsultantCompositionId->getProgramConsultantId(),
            'personnelId' => $programConsultantCompositionId->getPersonnelId(),
            'firmId' => $programConsultantCompositionId->getFirmId(),
        ];
        
        $qb = $this->createQueryBuilder('consultantComment');
        $qb->select('consultantComment')
                ->andWhere($qb->expr()->eq('consultantComment.id', ':consultantCommentId'))
                ->leftJoin('consultantComment.consultant', 'consultant')
                ->andWhere($qb->expr()->eq('consultant.removed', 'false'))
                ->andWhere($qb->expr()->eq('consultant.id', ':consultantId'))
                ->leftJoin('consultant.personnel', 'personnel')
                ->andWhere($qb->expr()->eq('personnel.removed', 'false'))
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
