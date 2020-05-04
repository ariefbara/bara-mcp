<?php

namespace Query\Infrastructure\Persistence\Doctrine\Repository;

use Doctrine\ORM\{
    EntityRepository,
    NoResultException
};
use Query\{
    Application\Service\Firm\Program\Participant\Worksheet\CommentRepository,
    Application\Service\Firm\Program\Participant\Worksheet\WorksheetCompositionId,
    Domain\Model\Firm\Program\Participant\Worksheet
};
use Resources\{
    Exception\RegularException,
    Infrastructure\Persistence\Doctrine\PaginatorBuilder
};

class CommentRepository extends EntityRepository implements CommentRepository
{

    public function all(WorksheetCompositionId $worksheetCompositionId, int $page, int $pageSize)
    {
        $params = [
            "worksheetId" => $worksheetCompositionId->getWorksheetId(),
            "participantId" => $worksheetCompositionId->getParticipantId(),
            "programId" => $worksheetCompositionId->getProgramId(),
            "firmId" => $worksheetCompositionId->getFirmId(),
        ];

        $qb = $this->createQueryBuilder('comment');
        $qb->select('comment')
                ->leftJoin('comment.worksheet', 'worksheet')
                ->andWhere($qb->expr()->eq('worksheet.removed', 'false'))
                ->andWhere($qb->expr()->eq('worksheet.id', ':worksheetId'))
                ->leftJoin('worksheet.participant', 'participant')
                ->andWhere($qb->expr()->eq('participant.active', 'true'))
                ->andWhere($qb->expr()->eq('participant.id', ':participantId'))
                ->leftJoin('participant.program', 'program')
                ->andWhere($qb->expr()->eq('program.removed', 'false'))
                ->andWhere($qb->expr()->eq('program.id', ':programId'))
                ->leftJoin('program.firm', 'firm')
                ->andWhere($qb->expr()->eq('firm.id', ':firmId'))
                ->setParameters($params);

        return PaginatorBuilder::build($qb->getQuery(), $page, $pageSize);
    }

    public function ofId(WorksheetCompositionId $worksheetCompositionId, string $commentId): Worksheet
    {
        $params = [
            "commentId" => $commentId,
            "worksheetId" => $worksheetCompositionId->getWorksheetId(),
            "participantId" => $worksheetCompositionId->getParticipantId(),
            "programId" => $worksheetCompositionId->getProgramId(),
            "firmId" => $worksheetCompositionId->getFirmId(),
        ];

        $qb = $this->createQueryBuilder('comment');
        $qb->select('comment')
                ->andWhere($qb->expr()->eq('comment.id', ':commentId'))
                ->leftJoin('comment.worksheet', 'worksheet')
                ->andWhere($qb->expr()->eq('worksheet.removed', 'false'))
                ->andWhere($qb->expr()->eq('worksheet.id', ':worksheetId'))
                ->leftJoin('worksheet.participant', 'participant')
                ->andWhere($qb->expr()->eq('participant.active', 'true'))
                ->andWhere($qb->expr()->eq('participant.id', ':participantId'))
                ->leftJoin('participant.program', 'program')
                ->andWhere($qb->expr()->eq('program.removed', 'false'))
                ->andWhere($qb->expr()->eq('program.id', ':programId'))
                ->leftJoin('program.firm', 'firm')
                ->andWhere($qb->expr()->eq('firm.id', ':firmId'))
                ->setParameters($params)
                ->setMaxResults(1);

        try {
            return $qb->getQuery()->getSingleResult();
        } catch (NoResultException $ex) {
            $errorDetail = "not found: comment not found";
            throw RegularException::notFound($errorDetail);
        }
    }

}
