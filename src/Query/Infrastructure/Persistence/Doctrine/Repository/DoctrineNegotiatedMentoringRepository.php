<?php

namespace Query\Infrastructure\Persistence\Doctrine\Repository;

use Doctrine\ORM\NoResultException;
use Query\Domain\Model\Firm\Program\Participant\MentoringRequest\NegotiatedMentoring;
use Query\Domain\Task\Dependency\Firm\Program\Participant\MentoringRequest\NegotiatedMentoringRepository;
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
                ->andWhere($qb->expr()->eq('negotiatedMentoring.id', ':id'))
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

    public function aNegotiatedMentoringBelongsToPersonnel(string $personnelId, string $id): NegotiatedMentoring
    {
        $parameters = [
            'personnelId' => $personnelId,
            'id' => $id,
        ];

        $qb = $this->createQueryBuilder('negotiatedMentoring');
        $qb->select('negotiatedMentoring')
                ->andWhere($qb->expr()->eq('negotiatedMentoring.id', ':id'))
                ->leftJoin('negotiatedMentoring.mentoringRequest', 'mentoringRequest')
                ->leftJoin('mentoringRequest.mentor', 'mentor')
                ->leftJoin('mentor.personnel', 'personnel')
                ->andWhere($qb->expr()->eq('personnel.id', ':personnelId'))
                ->setParameters($parameters)
                ->setMaxResults(1);

        try {
            return $qb->getQuery()->getSingleResult();
        } catch (NoResultException $ex) {
            throw RegularException::notFound('not found: negotiated mentoring not found');
        }
    }

}
