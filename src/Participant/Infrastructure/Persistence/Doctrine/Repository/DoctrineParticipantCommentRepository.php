<?php

namespace Participant\Infrastructure\Persistence\Doctrine\Repository;

use Doctrine\ORM\ {
    EntityRepository,
    NoResultException
};
use Participant\ {
    Application\Service\Participant\Worksheet\ParticipantCommentRepository,
    Domain\Model\ClientParticipant,
    Domain\Model\Participant\Worksheet\ParticipantComment
};
use Resources\ {
    Exception\RegularException,
    Uuid
};

class DoctrineParticipantCommentRepository extends EntityRepository implements ParticipantCommentRepository
{

    public function aParticipantCommentOfClientParticipant(
            string $firmId, string $clientId, string $programId, string $worksheetId, string $participantCommentId): ParticipantComment
    {
        $params = [
            'firmId' => $firmId,
            'clientId' => $clientId,
            'programId' => $programId,
            'worksheetId' => $worksheetId,
            'participantCommentId' => $participantCommentId,
        ];

        $clientParticipantQb = $this->getEntityManager()->createQueryBuilder();
        $clientParticipantQb->select('tParticipant.id')
                ->from(ClientParticipant::class, 'clientParticipant')
                ->leftJoin('clientParticipant.participant', 'tParticipant')
                ->leftJoin('clientParticipant.client', 'client')
                ->andWhere($clientParticipantQb->expr()->eq('client.id', ':clientId'))
                ->andWhere($clientParticipantQb->expr()->eq('client.firmId', ':firmId'))
                ->leftJoin('clientParticipant.program', 'program')
                ->andWhere($clientParticipantQb->expr()->eq('program.id', ':programId'))
                ->andWhere($clientParticipantQb->expr()->eq('program.firmId', ':firmId'))
                ->setMaxResults(1);
        
        $qb = $this->createQueryBuilder('participantComment');
        $qb->select('participantComment')
                ->andWhere($qb->expr()->eq('participantComment.id', ':participantCommentId'))
                ->leftJoin('participantComment.worksheet', 'worksheet')
                ->andWhere($qb->expr()->eq('worksheet.id', ':worksheetId'))
                ->leftJoin('worksheet.participant', 'participant')
                ->andWhere($qb->expr()->in('participant.id', $clientParticipantQb->getDQL()))
                ->setParameters($params)
                ->setMaxResults(1);
        
        try {
            return $qb->getQuery()->getSingleResult();
        } catch (NoResultException $ex) {
            $errorDetail = 'not found: consultant comment not found';
            throw RegularException::notFound($errorDetail);
        }
    }

    public function add(ParticipantComment $participantComment): void
    {
        $em = $this->getEntityManager();
        $em->persist($participantComment);
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

}
