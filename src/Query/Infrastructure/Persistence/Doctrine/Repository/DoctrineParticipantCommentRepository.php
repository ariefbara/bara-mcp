<?php

namespace Query\Infrastructure\Persistence\Doctrine\Repository;

use Doctrine\ORM\ {
    EntityRepository,
    NoResultException
};
use Query\ {
    Application\Service\Firm\Program\ClientParticipant\Worksheet\ParticipantCommentRepository,
    Domain\Model\Firm\Program\ClientParticipant,
    Domain\Model\Firm\Program\Participant\Worksheet\ParticipantComment
};
use Resources\Exception\RegularException;

class DoctrineParticipantCommentRepository extends EntityRepository implements ParticipantCommentRepository
{

    public function aParticipantCommentOfClientParticipant(
            string $firmId, string $programId, string $clientId, string $worksheetId, string $participantCommentId): ParticipantComment
    {
        $params = [
            'firmId' =>  $firmId,
            'programId' =>  $programId,
            'clientId' =>  $clientId,
            'worksheetId' =>  $worksheetId,
            'participantCommentId' => $participantCommentId,
        ];
        
        $clientParticipantQb = $this->getEntityManager()->createQueryBuilder();
        $clientParticipantQb->select('tParticipant.id')
                ->from(ClientParticipant::class, 'clientParticipant')
                ->leftJoin('clientParticipant.participant', 'tParticipant')
                ->leftJoin('clientParticipant.client', 'client')
                ->andWhere($clientParticipantQb->expr()->eq('client.id', ':clientId'))
                ->leftJoin('clientParticipant.program', 'program')
                ->andWhere($clientParticipantQb->expr()->eq('program.id', ':programId'))
                ->leftJoin('client.firm', 'cFirm')
                ->leftJoin('program.firm', 'pFirm')
                ->andWhere($clientParticipantQb->expr()->eq('cFirm.id', ':firmId'))
                ->andWhere($clientParticipantQb->expr()->eq('pFirm.id', ':firmId'))
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
            $errorDetail = 'not found: participant comment not found';
            throw RegularException::notFound($errorDetail);
        }
    }

}
