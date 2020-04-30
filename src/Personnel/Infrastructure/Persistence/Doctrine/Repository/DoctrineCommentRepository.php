<?php

namespace Personnel\Infrastructure\Persistence\Doctrine\Repository;

use Doctrine\ORM\ {
    EntityRepository,
    NoResultException
};
use Personnel\ {
    Application\Service\Firm\Personnel\PersonnelCompositionId,
    Application\Service\Firm\Program\Participant\Worksheet\CommentRepository,
    Domain\Model\Firm\Personnel\ProgramConsultant,
    Domain\Model\Firm\Program\Participant\Worksheet\Comment
};
use Resources\Exception\RegularException;

class DoctrineCommentRepository extends EntityRepository implements CommentRepository
{

    public function aCommentInProgramWorksheetWhereConsultantInvolved(
            PersonnelCompositionId $personnelCompositionId, string $programConsultantId, string $participantId,
            string $worksheetId, string $commentId): Comment
    {
        $parameters = [
            "commentId" => $commentId,
            "worksheetId" => $worksheetId,
            "participantId" => $participantId,
            "programConsultantId" => $programConsultantId,
            "personnelId" => $personnelCompositionId->getPersonnelId(),
            "firmId" => $personnelCompositionId->getFirmId(),
        ];
        
        $subQuery = $this->getEntityManager()->createQueryBuilder();
        $subQuery->select('tProgram.id')
                ->from(ProgramConsultant::class, "programConsultant")
                ->andWhere($subQuery->expr()->eq('programConsultant.removed', 'false'))
                ->andWhere($subQuery->expr()->eq('programConsultant.id', ':programConsultantId'))
                ->leftJoin('programConsultant.personnel', 'personnel')
                ->andWhere($subQuery->expr()->eq('personnel.removed', 'false'))
                ->andWhere($subQuery->expr()->eq('personnel.id', ':personnelId'))
                ->leftJoin('personnel.firm', 'firm')
                ->andWhere($subQuery->expr()->eq('firm.id', ':firmId'))
                ->leftJoin('programConsultant.program', 'tProgram')
                ->setMaxResults(1);
        
        $qb = $this->createQueryBuilder('comment');
        $qb->select('comment')
                ->andWhere($qb->expr()->eq('comment.removed', 'false'))
                ->andWhere($qb->expr()->eq('comment.id', ':commentId'))
                ->leftJoin('comment.worksheet', 'worksheet')
                ->andWhere($qb->expr()->eq('worksheet.removed', 'false'))
                ->andWhere($qb->expr()->eq('worksheet.id', ':worksheetId'))
                ->leftJoin('worksheet.participant', 'participant')
                ->andWhere($qb->expr()->eq('participant.active', 'true'))
                ->andWhere($qb->expr()->eq('participant.id', ':participantId'))
                ->leftJoin('participant.program', 'program')
                ->andWhere($qb->expr()->eq('program.removed', 'false'))
                ->andWhere($qb->expr()->in('program.id', $subQuery->getDQL()))
                ->setParameters($parameters)
                ->setMaxResults(1);
        
        try {
            return $qb->getQuery()->getSingleResult();
        } catch (NoResultException $ex) {
            $errorDetail = "not found: comment not found";
            throw RegularException::notFound($errorDetail);
        }
    }

}
