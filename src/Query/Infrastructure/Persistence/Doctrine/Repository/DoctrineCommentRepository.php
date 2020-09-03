<?php

namespace Query\Infrastructure\Persistence\Doctrine\Repository;

use Doctrine\ORM\ {
    EntityRepository,
    NoResultException
};
use Query\ {
    Application\Service\Firm\Program\Participant\Worksheet\CommentRepository,
    Domain\Model\Firm\Client\ClientParticipant,
    Domain\Model\Firm\Program\Participant\Worksheet\Comment,
    Domain\Model\User\UserParticipant
};
use Resources\ {
    Exception\RegularException,
    Infrastructure\Persistence\Doctrine\PaginatorBuilder
};

class DoctrineCommentRepository extends EntityRepository implements CommentRepository
{

    public function aCommentInClientWorksheet(string $firmId, string $clientId, string $programParticipationId,
            string $worksheetId, string $commentId): Comment
    {
        $params = [
            'firmId' => $firmId,
            'clientId' => $clientId,
            'programParticipationId' => $programParticipationId,
            'worksheetId' => $worksheetId,
            'commentId' => $commentId,
        ];

        $clientParticipantQb = $this->getEntityManager()->createQueryBuilder();
        $clientParticipantQb->select('t_participant.id')
                ->from(ClientParticipant::class, 'clientParticipant')
                ->andWhere($clientParticipantQb->expr()->eq('clientParticipant.id', ':programParticipationId'))
                ->leftJoin('clientParticipant.participant', 't_participant')
                ->leftJoin('clientParticipant.client', 'client')
                ->andWhere($clientParticipantQb->expr()->eq('client.id', ':clientId'))
                ->leftJoin('client.firm', 'firm')
                ->andWhere($clientParticipantQb->expr()->eq('firm.id', ':firmId'))
                ->setMaxResults(1);
        
        $qb = $this->createQueryBuilder('comment');
        $qb->select('comment')
                ->andWhere($qb->expr()->eq('comment.id', ':commentId'))
                ->leftJoin('comment.worksheet', 'worksheet')
                ->andWhere($qb->expr()->eq('worksheet.id', ':worksheetId'))
                ->leftJoin('worksheet.participant', 'participant')
                ->andWhere($qb->expr()->in('participant.id', $clientParticipantQb->getDQL()))
                ->setParameters($params)
                ->setMaxResults(1);
        
        try {
            return $qb->getQuery()->getSingleResult();
        } catch (NoResultException $ex) {
            $errorDetail = 'not found: comment not found';
            throw RegularException::notFound($errorDetail);
        }
    }

    public function all(string $firmId, string $programId, string $participantId, string $worksheetId, int $page,
            int $pageSize)
    {
        $params = [
            'firmId' => $firmId,
            'programId' => $programId,
            'participantId' => $participantId,
            'worksheetId' => $worksheetId,
        ];
        
        $qb = $this->createQueryBuilder('comment');
        $qb->select('comment')
                ->leftJoin('comment.worksheet', 'worksheet')
                ->andWhere($qb->expr()->eq('worksheet.id', ':worksheetId'))
                ->leftJoin('worksheet.participant', 'participant')
                ->andWhere($qb->expr()->eq('participant.id', ':participantId'))
                ->leftJoin('participant.program', 'program')
                ->andWhere($qb->expr()->eq('program.id', ':programId'))
                ->leftJoin('program.firm', 'firm')
                ->andWhere($qb->expr()->eq('firm.id', ':firmId'))
                ->setParameters($params);
        
        return PaginatorBuilder::build($qb->getQuery(), $page, $pageSize);
    }

    public function allCommentsInClientWorksheet(string $firmId, string $clientId, string $programParticipationId,
            string $worksheetId, int $page, int $pageSize)
    {
        $params = [
            'firmId' => $firmId,
            'clientId' => $clientId,
            'programParticipationId' => $programParticipationId,
            'worksheetId' => $worksheetId,
        ];

        $clientParticipantQb = $this->getEntityManager()->createQueryBuilder();
        $clientParticipantQb->select('t_participant.id')
                ->from(ClientParticipant::class, 'clientParticipant')
                ->andWhere($clientParticipantQb->expr()->eq('clientParticipant.id', ':programParticipationId'))
                ->leftJoin('clientParticipant.participant', 't_participant')
                ->leftJoin('clientParticipant.client', 'client')
                ->andWhere($clientParticipantQb->expr()->eq('client.id', ':clientId'))
                ->leftJoin('client.firm', 'firm')
                ->andWhere($clientParticipantQb->expr()->eq('firm.id', ':firmId'))
                ->setMaxResults(1);
        
        $qb = $this->createQueryBuilder('comment');
        $qb->select('comment')
                ->leftJoin('comment.worksheet', 'worksheet')
                ->andWhere($qb->expr()->eq('worksheet.id', ':worksheetId'))
                ->leftJoin('worksheet.participant', 'participant')
                ->andWhere($qb->expr()->in('participant.id', $clientParticipantQb->getDQL()))
                ->setParameters($params);
        
        return PaginatorBuilder::build($qb->getQuery(), $page, $pageSize);
    }

    public function ofId(string $firmId, string $programId, string $participantId, string $worksheetId,
            string $commentId): Comment
    {
        $params = [
            'firmId' => $firmId,
            'programId' => $programId,
            'participantId' => $participantId,
            'worksheetId' => $worksheetId,
            'commentId' => $commentId,
        ];
        
        $qb = $this->createQueryBuilder('comment');
        $qb->select('comment')
                ->andWhere($qb->expr()->eq('comment.id', ':commentId'))
                ->leftJoin('comment.worksheet', 'worksheet')
                ->andWhere($qb->expr()->eq('worksheet.id', ':worksheetId'))
                ->leftJoin('worksheet.participant', 'participant')
                ->andWhere($qb->expr()->eq('participant.id', ':participantId'))
                ->leftJoin('participant.program', 'program')
                ->andWhere($qb->expr()->eq('program.id', ':programId'))
                ->leftJoin('program.firm', 'firm')
                ->andWhere($qb->expr()->eq('firm.id', ':firmId'))
                ->setParameters($params)
                ->setMaxResults(1);
        
        try {
            return $qb->getQuery()->getSingleResult();
        } catch (NoResultException $ex) {
            $errorDetail = 'not found: comment not found';
            throw RegularException::notFound($errorDetail);
        }
    }

    public function aCommentInUserParticipantWorksheet(string $userId, string $userParticipantId, string $worksheetId,
            string $commentId): Comment
    {
        $params = [
            'userId' => $userId,
            'userParticipantId' => $userParticipantId,
            'worksheetId' => $worksheetId,
            'commentId' => $commentId,
        ];

        $userParticipantQb = $this->getEntityManager()->createQueryBuilder();
        $userParticipantQb->select('t_participant.id')
                ->from(UserParticipant::class, 'userParticipant')
                ->andWhere($userParticipantQb->expr()->eq('userParticipant.id', ':userParticipantId'))
                ->leftJoin('userParticipant.participant', 't_participant')
                ->leftJoin('userParticipant.user', 'user')
                ->andWhere($userParticipantQb->expr()->eq('user.id', ':userId'))
                ->setMaxResults(1);
        
        $qb = $this->createQueryBuilder('comment');
        $qb->select('comment')
                ->andWhere($qb->expr()->eq('comment.id', ':commentId'))
                ->leftJoin('comment.worksheet', 'worksheet')
                ->andWhere($qb->expr()->eq('worksheet.id', ':worksheetId'))
                ->leftJoin('worksheet.participant', 'participant')
                ->andWhere($qb->expr()->in('participant.id', $userParticipantQb->getDQL()))
                ->setParameters($params)
                ->setMaxResults(1);
        
        try {
            return $qb->getQuery()->getSingleResult();
        } catch (NoResultException $ex) {
            $errorDetail = 'not found: comment not found';
            throw RegularException::notFound($errorDetail);
        }
    }

    public function allCommentInUserParticipantWorksheet(string $userId, string $userParticipantId, string $worksheetId,
            int $page, int $pageSize)
    {
        $params = [
            'userId' => $userId,
            'userParticipantId' => $userParticipantId,
            'worksheetId' => $worksheetId,
        ];

        $userParticipantQb = $this->getEntityManager()->createQueryBuilder();
        $userParticipantQb->select('t_participant.id')
                ->from(UserParticipant::class, 'userParticipant')
                ->andWhere($userParticipantQb->expr()->eq('userParticipant.id', ':userParticipantId'))
                ->leftJoin('userParticipant.participant', 't_participant')
                ->leftJoin('userParticipant.user', 'user')
                ->andWhere($userParticipantQb->expr()->eq('user.id', ':userId'))
                ->setMaxResults(1);
        
        $qb = $this->createQueryBuilder('comment');
        $qb->select('comment')
                ->leftJoin('comment.worksheet', 'worksheet')
                ->andWhere($qb->expr()->eq('worksheet.id', ':worksheetId'))
                ->leftJoin('worksheet.participant', 'participant')
                ->andWhere($qb->expr()->in('participant.id', $userParticipantQb->getDQL()))
                ->setParameters($params);
        
        return PaginatorBuilder::build($qb->getQuery(), $page, $pageSize);
    }

}
