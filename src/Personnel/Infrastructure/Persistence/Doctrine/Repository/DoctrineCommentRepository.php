<?php

namespace Personnel\Infrastructure\Persistence\Doctrine\Repository;

use Doctrine\ORM\ {
    EntityRepository,
    NoResultException
};
use Personnel\ {
    Application\Service\Firm\Program\Participant\Worksheet\CommentRepository,
    Domain\Model\Firm\Personnel\ProgramConsultant,
    Domain\Model\Firm\Program\Participant\Worksheet\Comment
};
use Resources\Exception\RegularException;

class DoctrineCommentRepository extends EntityRepository implements CommentRepository
{
    
    public function aCommentInProgramWorksheetWhereConsultantInvolved(string $firmId, string $personnelId,
            string $programConsultationId, string $participantId, string $worksheetId, string $commentId): Comment
    {
        $params = [
            'firmId' => $firmId,
            'personnelId' => $personnelId,
            'programConsultationId' => $programConsultationId,
            'participantId' => $participantId,
            'worksheetId' => $worksheetId,
            'commentId' => $commentId,
        ];
        
        $programConsultationQb = $this->getEntityManager()->createQueryBuilder();
        $programConsultationQb->select('programConsultation.programId')
                ->from(ProgramConsultant::class, 'programConsultation')
                ->andWhere($programConsultationQb->expr()->eq('programConsultation.id', ':programConsultationId'))
                ->leftJoin('programConsultation.personnel', 'personnel')
                ->andWhere($programConsultationQb->expr()->eq('personnel.id', ':personnelId'))
                ->andWhere($programConsultationQb->expr()->eq('personnel.firmId', ':firmId'))
                ->setMaxResults(1);
        
        $qb = $this->createQueryBuilder('comment');
        $qb->select('comment')
                ->andWhere($qb->expr()->eq('comment.id', ':commentId'))
                ->leftJoin('comment.worksheet', 'worksheet')
                ->andWhere($qb->expr()->eq('worksheet.id', ':worksheetId'))
                ->leftJoin('worksheet.participant', 'participant')
                ->andWhere($qb->expr()->eq('participant.id', ':participantId'))
                ->andWhere($qb->expr()->in('participant.programId', $programConsultationQb->getDQL()))
                ->setParameters($params)
                ->setMaxResults(1);
        
        try {
            return $qb->getQuery()->getSingleResult();
        } catch (NoResultException $ex) {
            $errorDetail = 'not found: comment not found';
            throw RegularException::notFound($errorDetail);
        }
    }

}
