<?php

namespace Notification\Infrastructure\Persistence\Doctrine\Repository;

use Doctrine\ORM\ {
    EntityRepository,
    NoResultException
};
use Notification\ {
    Application\Service\Firm\Program\Participant\Worksheet\CommentRepository,
    Domain\Model\Firm\Program\Participant\Worksheet\Comment
};
use Query\Domain\Model\ {
    Firm\Client\ClientParticipant,
    User\UserParticipant
};
use Resources\Exception\RegularException;

class DoctrineCommentRepository extends EntityRepository implements CommentRepository
{
    
    public function aCommentInClientParticipantWorksheet(string $firmId, string $clientId,
            string $programParticipationId, string $worksheetId, string $commentId): Comment
    {
        $params = [
            'firmId' => $firmId,
            'clientId' => $clientId,
            'programParticipationId' => $programParticipationId,
            'worksheetId' => $worksheetId,
            'commentId' => $commentId,
        ];
        
        $clientParticipantQb = $this->getEntityManager()->createQueryBuilder();
        $clientParticipantQb->select('cp_participant.id')
                ->from(ClientParticipant::class, 'clientParticipant')
                ->andWhere($clientParticipantQb->expr()->eq('clientParticipant.id', ':programParticipationId'))
                ->leftJoin('clientParticipant.participant', 'cp_participant')
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

    public function aCommentInUserParticipantWorksheet(string $userId, string $programParticipationId,
            string $worksheetId, string $commentId): Comment
    {
        $params = [
            'userId' => $userId,
            'programParticipationId' => $programParticipationId,
            'worksheetId' => $worksheetId,
            'commentId' => $commentId,
        ];
        
        $userParticipantQb = $this->getEntityManager()->createQueryBuilder();
        $userParticipantQb->select('up_participant.id')
                ->from(UserParticipant::class, 'userParticipant')
                ->andWhere($userParticipantQb->expr()->eq('userParticipant.id', ':programParticipationId'))
                ->leftJoin('userParticipant.participant', 'up_participant')
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

}
