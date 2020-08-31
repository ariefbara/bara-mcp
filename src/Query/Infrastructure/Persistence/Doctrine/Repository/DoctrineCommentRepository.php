<?php

namespace Query\Infrastructure\Persistence\Doctrine\Repository;

use Doctrine\ORM\ {
    EntityRepository,
    NoResultException
};
use Query\ {
    Application\Service\Firm\Program\Participant\Worksheet\CommentRepository,
    Domain\Model\Firm\Program\ClientParticipant,
    Domain\Model\Firm\Program\Participant\Worksheet\Comment,
    Domain\Model\Firm\Program\Participant\Worksheet\ConsultantComment,
    Domain\Model\Firm\Program\UserParticipant
};
use Resources\ {
    Exception\RegularException,
    Infrastructure\Persistence\Doctrine\PaginatorBuilder
};


class DoctrineCommentRepository extends EntityRepository implements CommentRepository
{
    public function aCommentInClientWorksheet(
            string $firmId, string $clientId, string $programId, string $worksheetId,
            string $commentId): Comment
    {
        $params = [
            'firmId' => $firmId,
            'clientId' => $clientId,
            'programId' => $programId,
            'worksheetId' => $worksheetId,
            'commentId' => $commentId,
        ];
        
        $clientParticipantQb = $this->getEntityManager()->createQueryBuilder();
        $clientParticipantQb->select('cp_participant.id')
                ->from(ClientParticipant::class, 'clientParticipant')
                ->leftJoin('clientParticipant.participant', 'cp_participant')
                ->leftJoin('clientParticipant.client', 'cp_client')
                ->andWhere($clientParticipantQb->expr()->eq('cp_client.id', ':clientId'))
                ->leftJoin('cp_client.firm', 'cp_c_firm')
                ->andWhere($clientParticipantQb->expr()->eq('cp_c_firm.id', ':firmId'))
                ->leftJoin('clientParticipant.program', 'cp_program')
                ->andWhere($clientParticipantQb->expr()->eq('cp_program.id', ':programId'))
                ->leftJoin('cp_program.firm', 'cp_p_firm')
                ->andWhere($clientParticipantQb->expr()->eq('cp_p_firm.id', ':firmId'))
                ->setMaxResults(1);
        
        $consultantCommentQb = $this->getEntityManager()->createQueryBuilder();
        $consultantCommentQb->select('cc_comment.id')
                ->from(ConsultantComment::class, 'consultantComment')
                ->leftJoin('consultantComment.comment', 'cc_comment')
                ->andWhere($consultantCommentQb->expr()->eq('cc_comment.id', ':commentId'))
                ->leftJoin('consultantComment.worksheet', 'cc_worksheet')
                ->andWhere($consultantCommentQb->expr()->eq('cc_worksheet.id', ':worksheetId'))
                ->leftJoin('cc_worksheet.participant', 'cc_participant')
                ->andWhere($consultantCommentQb->expr()->in('cc_participant.id', $clientParticipantQb->getDQL()))
                ->setMaxResults(1);
        
        $participantCommentQb = $this->getEntityManager()->createQueryBuilder();
        $participantCommentQb->select('pc_comment.id')
                ->from(ConsultantComment::class, 'participantComment')
                ->leftJoin('participantComment.comment', 'pc_comment')
                ->andWhere($participantCommentQb->expr()->eq('pc_comment.id', ':commentId'))
                ->leftJoin('participantComment.worksheet', 'pc_worksheet')
                ->andWhere($participantCommentQb->expr()->eq('pc_worksheet.id', ':worksheetId'))
                ->leftJoin('pc_worksheet.participant', 'pc_participant')
                ->andWhere($participantCommentQb->expr()->in('pc_participant.id', $clientParticipantQb->getDQL()))
                ->setMaxResults(1);
        
        $qb = $this->createQueryBuilder('comment');
        $qb->select('comment')
                ->andWhere($qb->expr()->orX(
                        $qb->expr()->in('comment.id', $consultantCommentQb->getDQL()),
                        $qb->expr()->in('comment.id', $participantCommentQb->getDQL()),
                ))
                ->setParameters($params)
                ->setMaxResults(1);
        
        try {
            return $qb->getQuery()->getSingleResult();
        } catch (NoResultException $ex) {
            $errroDetail = 'not found: comment not found';
            throw RegularException::notFound($errroDetail);
        }
    }

    public function all(
            string $firmId, string $programId, string $participantId, string $worksheetId, int $page,
            int $pageSize)
    {
        $params = [
            'firmId' => $firmId,
            'programId' => $programId,
            'participantId' => $participantId,
            'worksheetId' => $worksheetId,
        ];
        
        $clientParticipantQb = $this->getEntityManager()->createQueryBuilder();
        $clientParticipantQb->select('cp_participant.id')
                ->from(ClientParticipant::class, 'clientParticipant')
                ->leftJoin('clientParticipant.participant', 'cp_participant')
                ->andWhere($clientParticipantQb->expr()->eq('cp_participant.id', ':participantId'))
                ->leftJoin('clientParticipant.program', 'cp_program')
                ->andWhere($clientParticipantQb->expr()->eq('cp_program.id', ':programId'))
                ->leftJoin('cp_program.firm', 'cp_firm')
                ->andWhere($clientParticipantQb->expr()->eq('cp_firm.id', ':firmId'))
                ->setMaxResults(1);
        
        $userParticipantQb = $this->getEntityManager()->createQueryBuilder();
        $userParticipantQb->select('up_participant.id')
                ->from(UserParticipant::class, 'userParticipant')
                ->leftJoin('userParticipant.participant', 'up_participant')
                ->andWhere($userParticipantQb->expr()->eq('up_participant.id', ':participantId'))
                ->leftJoin('userParticipant.program', 'up_program')
                ->andWhere($userParticipantQb->expr()->eq('up_program.id', ':programId'))
                ->leftJoin('up_program.firm', 'up_firm')
                ->andWhere($userParticipantQb->expr()->eq('up_firm.id', ':firmId'))
                ->setMaxResults(1);
        
        $consultantCommentQb = $this->getEntityManager()->createQueryBuilder();
        $consultantCommentQb->select('cc_comment.id')
                ->from(ConsultantComment::class, 'consultantComment')
                ->leftJoin('consultantComment.comment', 'cc_comment')
                ->leftJoin('consultantComment.worksheet', 'cc_worksheet')
                ->andWhere($consultantCommentQb->expr()->eq('cc_worksheet.id', ':worksheetId'))
                ->leftJoin('cc_worksheet.participant', 'cc_participant')
                ->andWhere($consultantCommentQb->expr()->orX(
                        $consultantCommentQb->expr()->in('cc_participant.id', $clientParticipantQb->getDQL()),
                        $consultantCommentQb->expr()->in('cc_participant.id', $userParticipantQb->getDQL())
                ))
                ->setMaxResults(1);
        
        $participantCommentQb = $this->getEntityManager()->createQueryBuilder();
        $participantCommentQb->select('pc_comment.id')
                ->from(ConsultantComment::class, 'participantComment')
                ->leftJoin('participantComment.comment', 'pc_comment')
                ->leftJoin('participantComment.worksheet', 'pc_worksheet')
                ->andWhere($participantCommentQb->expr()->eq('pc_worksheet.id', ':worksheetId'))
                ->leftJoin('pc_worksheet.participant', 'pc_participant')
                ->andWhere($consultantCommentQb->expr()->orX(
                        $participantCommentQb->expr()->in('pc_participant.id', $clientParticipantQb->getDQL()),
                        $participantCommentQb->expr()->in('pc_participant.id', $userParticipantQb->getDQL())
                ))
                ->setMaxResults(1);
        
        $qb = $this->createQueryBuilder('comment');
        $qb->select('comment')
                ->andWhere($qb->expr()->orX(
                        $qb->expr()->in('comment.id', $consultantCommentQb->getDQL()),
                        $qb->expr()->in('comment.id', $participantCommentQb->getDQL()),
                ))
                ->setParameters($params);
        
        return PaginatorBuilder::build($qb->getQuery(), $page, $pageSize);
    }

    public function allCommentsInClientWorksheet(
            string $firmId, string $clientId, string $programId,
            string $worksheetId, int $page, int $pageSize)
    {
        $params = [
            'firmId' => $firmId,
            'clientId' => $clientId,
            'programId' => $programId,
            'worksheetId' => $worksheetId,
        ];
        
        $clientParticipantQb = $this->getEntityManager()->createQueryBuilder();
        $clientParticipantQb->select('cp_participant.id')
                ->from(ClientParticipant::class, 'clientParticipant')
                ->leftJoin('clientParticipant.participant', 'cp_participant')
                ->leftJoin('clientParticipant.client', 'cp_client')
                ->andWhere($clientParticipantQb->expr()->eq('cp_client.id', ':clientId'))
                ->leftJoin('cp_client.firm', 'cp_c_firm')
                ->andWhere($clientParticipantQb->expr()->eq('cp_c_firm.id', ':firmId'))
                ->leftJoin('clientParticipant.program', 'cp_program')
                ->andWhere($clientParticipantQb->expr()->eq('cp_program.id', ':programId'))
                ->leftJoin('cp_program.firm', 'cp_p_firm')
                ->andWhere($clientParticipantQb->expr()->eq('cp_p_firm.id', ':firmId'))
                ->setMaxResults(1);
        
        $consultantCommentQb = $this->getEntityManager()->createQueryBuilder();
        $consultantCommentQb->select('cc_comment.id')
                ->from(ConsultantComment::class, 'consultantComment')
                ->leftJoin('consultantComment.comment', 'cc_comment')
                ->leftJoin('consultantComment.worksheet', 'cc_worksheet')
                ->andWhere($consultantCommentQb->expr()->eq('cc_worksheet.id', ':worksheetId'))
                ->leftJoin('cc_worksheet.participant', 'cc_participant')
                ->andWhere($consultantCommentQb->expr()->in('cc_participant.id', $clientParticipantQb->getDQL()))
                ->setMaxResults(1);
        
        $participantCommentQb = $this->getEntityManager()->createQueryBuilder();
        $participantCommentQb->select('pc_comment.id')
                ->from(ConsultantComment::class, 'participantComment')
                ->leftJoin('participantComment.comment', 'pc_comment')
                ->leftJoin('participantComment.worksheet', 'pc_worksheet')
                ->andWhere($participantCommentQb->expr()->eq('pc_worksheet.id', ':worksheetId'))
                ->leftJoin('pc_worksheet.participant', 'pc_participant')
                ->andWhere($participantCommentQb->expr()->in('pc_participant.id', $clientParticipantQb->getDQL()))
                ->setMaxResults(1);
        
        $qb = $this->createQueryBuilder('comment');
        $qb->select('comment')
                ->andWhere($qb->expr()->orX(
                        $qb->expr()->in('comment.id', $consultantCommentQb->getDQL()),
                        $qb->expr()->in('comment.id', $participantCommentQb->getDQL()),
                ))
                ->setParameters($params);
        
        return PaginatorBuilder::build($qb->getQuery(), $page, $pageSize);
    }

    public function ofId(
            string $firmId, string $programId, string $participantId, string $worksheetId,
            string $commentId): Comment
    {
        $params = [
            'firmId' => $firmId,
            'programId' => $programId,
            'participantId' => $participantId,
            'worksheetId' => $worksheetId,
            'commentId' => $commentId,
        ];
        
        $clientParticipantQb = $this->getEntityManager()->createQueryBuilder();
        $clientParticipantQb->select('cp_participant.id')
                ->from(ClientParticipant::class, 'clientParticipant')
                ->leftJoin('clientParticipant.participant', 'cp_participant')
                ->andWhere($clientParticipantQb->expr()->eq('cp_participant.id', ':participantId'))
                ->leftJoin('clientParticipant.program', 'cp_program')
                ->andWhere($clientParticipantQb->expr()->eq('cp_program.id', ':programId'))
                ->leftJoin('cp_program.firm', 'cp_firm')
                ->andWhere($clientParticipantQb->expr()->eq('cp_firm.id', ':firmId'))
                ->setMaxResults(1);
        
        $userParticipantQb = $this->getEntityManager()->createQueryBuilder();
        $userParticipantQb->select('up_participant.id')
                ->from(UserParticipant::class, 'userParticipant')
                ->leftJoin('userParticipant.participant', 'up_participant')
                ->andWhere($userParticipantQb->expr()->eq('up_participant.id', ':participantId'))
                ->leftJoin('userParticipant.program', 'up_program')
                ->andWhere($userParticipantQb->expr()->eq('up_program.id', ':programId'))
                ->leftJoin('up_program.firm', 'up_firm')
                ->andWhere($userParticipantQb->expr()->eq('up_firm.id', ':firmId'))
                ->setMaxResults(1);
        
        $consultantCommentQb = $this->getEntityManager()->createQueryBuilder();
        $consultantCommentQb->select('cc_comment.id')
                ->from(ConsultantComment::class, 'consultantComment')
                ->leftJoin('consultantComment.comment', 'cc_comment')
                ->andWhere($consultantCommentQb->expr()->eq('cc_comment.id', ':commentId'))
                ->leftJoin('consultantComment.worksheet', 'cc_worksheet')
                ->andWhere($consultantCommentQb->expr()->eq('cc_worksheet.id', ':worksheetId'))
                ->leftJoin('cc_worksheet.participant', 'cc_participant')
                ->andWhere($consultantCommentQb->expr()->orX(
                        $consultantCommentQb->expr()->in('cc_participant.id', $clientParticipantQb->getDQL()),
                        $consultantCommentQb->expr()->in('cc_participant.id', $userParticipantQb->getDQL())
                ))
                ->setMaxResults(1);
        
        $participantCommentQb = $this->getEntityManager()->createQueryBuilder();
        $participantCommentQb->select('pc_comment.id')
                ->from(ConsultantComment::class, 'participantComment')
                ->leftJoin('participantComment.comment', 'pc_comment')
                ->andWhere($participantCommentQb->expr()->eq('pc_comment.id', ':commentId'))
                ->leftJoin('participantComment.worksheet', 'pc_worksheet')
                ->andWhere($participantCommentQb->expr()->eq('pc_worksheet.id', ':worksheetId'))
                ->leftJoin('pc_worksheet.participant', 'pc_participant')
                ->andWhere($consultantCommentQb->expr()->orX(
                        $participantCommentQb->expr()->in('pc_participant.id', $clientParticipantQb->getDQL()),
                        $participantCommentQb->expr()->in('pc_participant.id', $userParticipantQb->getDQL())
                ))
                ->setMaxResults(1);
        
        $qb = $this->createQueryBuilder('comment');
        $qb->select('comment')
                ->andWhere($qb->expr()->orX(
                        $qb->expr()->in('comment.id', $consultantCommentQb->getDQL()),
                        $qb->expr()->in('comment.id', $participantCommentQb->getDQL()),
                ))
                ->setParameters($params)
                ->setMaxResults(1);
        
        try {
            return $qb->getQuery()->getSingleResult();
        } catch (NoResultException $ex) {
            $errroDetail = 'not found: comment not found';
            throw RegularException::notFound($errroDetail);
        }
    }

}
