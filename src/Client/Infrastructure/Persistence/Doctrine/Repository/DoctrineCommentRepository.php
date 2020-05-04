<?php

namespace Client\Infrastructure\Persistence\Doctrine\Repository;

use Client\ {
    Application\Listener\CommentRepository as InterfaceForListener,
    Application\Service\Client\ProgramParticipation\Worksheet\CommentRepository,
    Domain\Model\Client\ProgramParticipation\Worksheet\Comment
};
use Doctrine\ORM\ {
    EntityRepository,
    NoResultException
};
use Query\ {
    Application\Service\Firm\Program\Participant\Worksheet\WorksheetCompositionId,
    Domain\Model\Firm\Program\Consultant\ConsultantComment
};
use Resources\Exception\RegularException;

class DoctrineCommentRepository extends EntityRepository implements CommentRepository, InterfaceForListener
{

    public function ofId(WorksheetCompositionId $worksheetCompositionId, string $commentId): Comment
    {
        $parameters = [
            "commentId" => $commentId,
            "worksheetId" => $worksheetCompositionId->getWorksheetId(),
            "programParticipationId" => $worksheetCompositionId->getProgramParticipationId(),
            "clientId" => $worksheetCompositionId->getClientId(),
        ];

        $qb = $this->createQueryBuilder("comment");
        $qb->select('comment')
                ->andWhere($qb->expr()->eq('comment.removed', "false"))
                ->andWhere($qb->expr()->eq('comment.id', ":commentId"))
                ->leftJoin("comment.worksheet", "worksheet")
                ->andWhere($qb->expr()->eq('worksheet.removed', "false"))
                ->andWhere($qb->expr()->eq('worksheet.id', ":worksheetId"))
                ->leftJoin("worksheet.programParticipation", "programParticipation")
                ->andWhere($qb->expr()->eq('programParticipation.active', "true"))
                ->andWhere($qb->expr()->eq('programParticipation.id', ":programParticipationId"))
                ->leftJoin("programParticipation.client", "client")
                ->andWhere($qb->expr()->eq('client.id', ":clientId"))
                ->setParameters($parameters)
                ->setMaxResults(1);

        try {
            return $qb->getQuery()->getSingleResult();
        } catch (NoResultException $ex) {
            $errorDetail = "not found: comment not found";
            throw RegularException::notFound($errorDetail);
        }
    }

    public function aCommentFromConsultant(
            string $firmId, string $personnelId, string $consultantId, string $consultantCommentId): Comment
    {

        $parameters = [
            "consultantCommentId" => $consultantCommentId,
            "consultantId" => $consultantCommentId,
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

}
