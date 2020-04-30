<?php

namespace Client\Infrastructure\Persistence\Doctrine\Repository;

use Client\ {
    Application\Service\Client\ProgramParticipation\Worksheet\CommentRepository,
    Application\Service\Client\ProgramParticipation\Worksheet\WorksheetCompositionId,
    Domain\Model\Client\ProgramParticipation\Worksheet\Comment,
    Domain\Model\Firm\Program\Consultant\ConsultantComment
};
use Doctrine\ORM\ {
    EntityRepository,
    NoResultException
};
use Personnel\Application\Service\Firm\Personnel\ProgramConsultant\ProgramConsultantCompositionId;
use Resources\ {
    Exception\RegularException,
    Infrastructure\Persistence\Doctrine\PaginatorBuilder
};

class DoctrineCommentRepository extends EntityRepository implements CommentRepository
{

    public function all(WorksheetCompositionId $worksheetCompositionId, int $page, int $pageSize)
    {
        $parameters = [
            "worksheetId" => $worksheetCompositionId->getWorksheetId(),
            "programParticipationId" => $worksheetCompositionId->getProgramParticipationId(),
            "clientId" => $worksheetCompositionId->getClientId(),
        ];

        $qb = $this->createQueryBuilder("comment");
        $qb->select('comment')
                ->andWhere($qb->expr()->eq('comment.removed', "false"))
                ->leftJoin("comment.worksheet", "worksheet")
                ->andWhere($qb->expr()->eq('worksheet.removed', "false"))
                ->andWhere($qb->expr()->eq('worksheet.id', ":worksheetId"))
                ->leftJoin("worksheet.programParticipation", "programParticipation")
                ->andWhere($qb->expr()->eq('programParticipation.active', "true"))
                ->andWhere($qb->expr()->eq('programParticipation.id', ":programParticipationId"))
                ->leftJoin("programParticipation.client", "client")
                ->andWhere($qb->expr()->eq('client.id', ":clientId"))
                ->setParameters($parameters);

        return PaginatorBuilder::build($qb->getQuery(), $page, $pageSize);
    }

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
            ProgramConsultantCompositionId $programConsultantCompositionid, string $consultantCommentId): Comment
    {
        $parameters = [
            "consultantCommentId" => $consultantCommentId,
            "consultantId" => $programConsultantCompositionid->getProgramConsultantId(),
            "personnelId" => $programConsultantCompositionid->getPersonnelId(),
            "firmId" => $programConsultantCompositionid->getFirmId(),
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
