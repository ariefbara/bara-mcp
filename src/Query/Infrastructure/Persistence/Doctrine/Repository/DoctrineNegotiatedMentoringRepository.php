<?php

namespace Query\Infrastructure\Persistence\Doctrine\Repository;

use Doctrine\ORM\NoResultException;
use Query\Domain\Model\Firm\Program\Participant\MentoringRequest\NegotiatedMentoring;
use Query\Domain\Task\Dependency\Firm\Program\Participant\NegotiatedMentoringRepository;
use Resources\Exception\RegularException;
use Resources\Infrastructure\Persistence\Doctrine\Repository\DoctrineEntityRepository;

class DoctrineNegotiatedMentoringRepository extends DoctrineEntityRepository implements NegotiatedMentoringRepository
{

    public function aNegotiatedMentoringBelongsToParticipant(string $participantId, string $id): NegotiatedMentoring
    {
        $parameters = [
            'participantId' => $participantId,
            'id' => $id,
        ];

        $qb = $this->createQueryBuilder('negotiatedMentoring');
        $qb->select('negotiatedMentoring')
                ->andWhere($qb->expr()->eq('negotiatedMentoring.id', ':negotiatedMentoringId'))
                ->leftJoin('negotiatedMentoring.mentoringRequest', 'mentoringRequest')
                ->leftJoin('mentoringRequest.participant', 'participant')
                ->andWhere($qb->expr()->eq('participant.id', ':participantId'))
                ->setParameters($parameters)
                ->setMaxResults(1);

        try {
            return $qb->getQuery()->getSingleResult();
        } catch (NoResultException $ex) {
            throw RegularException::notFound('not found: negotiated mentoring not found');
        }
    }

}
