<?php

namespace Participant\Infrastructure\Persistence\Doctrine\Repository;

use Doctrine\ORM\ {
    EntityRepository,
    NoResultException
};
use Participant\ {
    Application\Service\Participant\Worksheet\CommentRepository,
    Domain\Model\ClientParticipant,
    Domain\Model\Participant\Worksheet\Comment,
    Domain\Model\TeamProgramParticipation,
    Domain\Model\UserParticipant
};
use Resources\ {
    Exception\RegularException,
    Uuid
};

class DoctrineCommentRepository extends EntityRepository implements CommentRepository
{

    public function aCommentInClientParticipantWorksheet(
            string $firmId, string $clientId, string $programParticipationId, string $worksheetId, string $commentId): Comment
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
                ->andWhere($clientParticipantQb->expr()->eq('client.firmId', ':firmId'))
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

    public function aCommentInUserParticipantWorksheet(
            string $userId, string $programParticipationId, string $worksheetId, string $commentId): Comment
    {
        $params = [
            'userId' => $userId,
            'programParticipationId' => $programParticipationId,
            'worksheetId' => $worksheetId,
            'commentId' => $commentId,
        ];

        $userParticipantQb = $this->getEntityManager()->createQueryBuilder();
        $userParticipantQb->select('tParticipant.id')
                ->from(UserParticipant::class, 'userParticipant')
                ->andWhere($userParticipantQb->expr()->eq('userParticipant.id', ':programParticipationId'))
                ->andWhere($userParticipantQb->expr()->eq('userParticipant.userId', ':userId'))
                ->leftJoin('userParticipant.participant', 'tParticipant')
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

    public function add(Comment $comment): void
    {
        $em = $this->getEntityManager();
        $em->persist($comment);
        $em->flush();
    }

    public function nextIdentity(): string
    {
        return Uuid::generateUuid4();
    }

    public function update(): void
    {
        $this->getEntityManager()->flush();
    }

    public function aCommentBelongsToTeamParticipant(string $teamProgramParticipationId, string $worksheetId,
            string $commentId): Comment
    {
        $params = [
            "teamProgramParticipationId" => $teamProgramParticipationId,
            "worksheetId" => $worksheetId,
            "commentId" => $commentId,
        ];
        
        $participantQb = $this->getEntityManager()->createQueryBuilder();
        $participantQb->select("programParticipation.id")
                ->from(TeamProgramParticipation::class, "teamProgramParticipation")
                ->andWhere($participantQb->expr()->eq("teamProgramParticipation.id", ":teamProgramParticipationId"))
                ->leftJoin("teamProgramParticipation.programParticipation", "programParticipation")
                ->setMaxResults(1);
        
        $qb = $this->createQueryBuilder("comment");
        $qb->select("comment")
                ->andWhere($qb->expr()->eq("comment.id", ":commentId"))
                ->leftJoin("comment.worksheet", "worksheet")
                ->andWhere($qb->expr()->eq("worksheet.id", ":worksheetId"))
                ->leftJoin("worksheet.participant", "participant")
                ->andWhere($qb->expr()->in("participant.id", $participantQb->getDQL()))
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
