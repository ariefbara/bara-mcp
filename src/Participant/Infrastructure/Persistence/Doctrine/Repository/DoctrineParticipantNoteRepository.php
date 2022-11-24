<?php

namespace Participant\Infrastructure\Persistence\Doctrine\Repository;

use Doctrine\ORM\NoResultException;
use Participant\Domain\Model\Participant\ParticipantNote;
use Participant\Domain\Task\Dependency\Firm\Program\Participant\ParticipantNoteRepository;
use Resources\Exception\RegularException;
use Resources\Infrastructure\Persistence\Doctrine\Repository\DoctrineEntityRepository;

class DoctrineParticipantNoteRepository extends DoctrineEntityRepository implements ParticipantNoteRepository
{

    public function add(ParticipantNote $participantNote): void
    {
        $this->persist($participantNote);
    }

    public function ofId(string $id): ParticipantNote
    {
        $parameters = [
            'id' => $id,
        ];

        $qb = $this->createQueryBuilder('participantNote');
        $qb->select('participantNote')
                ->andWhere($qb->expr()->eq('participantNote.id', ':id'))
                ->leftJoin('participantNote.note', 'note')
                ->andWhere($qb->expr()->eq('note.removed', 'false'))
                ->setParameters($parameters)
                ->setMaxResults(1);

        try {
            return $qb->getQuery()->getSingleResult();
        } catch (NoResultException $ex) {
            throw RegularException::notFound('participant note not found');
        }
    }

}
