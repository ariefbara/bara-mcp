<?php

namespace User\Infrastructure\Persistence\Doctrine\Repository;

use User\{
    Application\Listener\CommentRepository as InterfaceForListener,
    Application\Service\User\ProgramParticipation\Worksheet\CommentRepository,
    Application\Service\User\ProgramParticipation\Worksheet\WorksheetCompositionId,
    Domain\Model\User\ProgramParticipation\Worksheet\Comment
};
use Doctrine\ORM\{
    EntityRepository,
    NoResultException
};
use Query\Domain\Model\Firm\Program\Consultant\ConsultantComment;
use Resources\Exception\RegularException;

class DoctrineCommentRepository extends EntityRepository implements CommentRepository, InterfaceForListener
{

    public function aCommentFromConsultant(
            string $firmId, string $personnelId, string $consultantId, string $consultantCommentId): Comment
    {

        $parameters = [
            "consultantCommentId" => $consultantCommentId,
            "consultantId" => $consultantId,
            "personnelId" => $personnelId,
            "firmId" => $firmId,
        ];

        $subQuery = $this->getEntityManager()->createQueryBuilder();
        $subQuery->select('tComment.id')
                ->from(ConsultantComment::class, "consultantComment")
                ->andWhere($subQuery->expr()->eq('consultantComment.id', ':consultantCommentId'))
                ->leftJoin('consultantComment.comment', 'tComment')
                ->andWhere($subQuery->expr()->eq('tComment.removed', 'false'))
                ->leftJoin('consultantComment.consultant', 'consultant')
                ->andWhere($subQuery->expr()->eq('consultant.id', ':consultantId'))
                ->leftJoin('consultant.personnel', 'personnel')
                ->andWhere($subQuery->expr()->eq('personnel.id', ':personnelId'))
                ->leftJoin('personnel.firm', 'firm')
                ->andWhere($subQuery->expr()->eq('firm.id', ':firmId'))
                ->setMaxResults(1);

        $qb = $this->createQueryBuilder("comment");
        $qb->select('comment')
                ->andWhere($qb->expr()->eq('comment.removed', "false"))
                ->andWhere($qb->expr()->in('comment.id', $subQuery->getDQL()))
                ->setParameters($parameters)
                ->setMaxResults(1);

        try {
            return $qb->getQuery()->getSingleResult();
        } catch (NoResultException $ex) {
            $errorDetail = "not found: comment not found";
            throw RegularException::notFound($errorDetail);
        }
    }

    public function ofId(WorksheetCompositionId $worksheetCompositionId, string $commentId): Comment
    {
        $params = [
            'commentId' => $commentId,
            'worksheetId' => $worksheetCompositionId->getWorksheetId(),
            'programParticipationId' => $worksheetCompositionId->getProgramParticipationId(),
            'userId' => $worksheetCompositionId->getUserId(),
        ];

        $qb = $this->createQueryBuilder('comment');
        $qb->select('comment')
                ->andWhere($qb->expr()->eq('comment.removed', 'false'))
                ->andWhere($qb->expr()->eq('comment.id', ':commentId'))
                ->leftJoin('comment.worksheet', 'worksheet')
                ->andWhere($qb->expr()->eq('worksheet.removed', 'false'))
                ->andWhere($qb->expr()->eq('worksheet.id', ':worksheetId'))
                ->leftJoin('worksheet.programParticipation', 'programParticipation')
                ->andWhere($qb->expr()->eq('programParticipation.active', 'true'))
                ->andWhere($qb->expr()->eq('programParticipation.id', ':programParticipationId'))
                ->leftJoin('programParticipation.user', 'user')
                ->andWhere($qb->expr()->eq('user.id', ':userId'))
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
