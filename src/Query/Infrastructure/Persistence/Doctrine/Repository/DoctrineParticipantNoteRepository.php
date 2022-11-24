<?php

namespace Query\Infrastructure\Persistence\Doctrine\Repository;

use Doctrine\ORM\EntityRepository;
use Doctrine\ORM\NoResultException;
use Query\Domain\Model\Firm\Program\Participant\ParticipantNote;
use Query\Domain\Task\Dependency\Firm\Program\Participant\ParticipantNoteRepository;
use Resources\Exception\RegularException;

class DoctrineParticipantNoteRepository extends EntityRepository implements ParticipantNoteRepository
{
    
    public function aParticipantNoteBelongsToParticipant(string $participantId, string $id): ParticipantNote
    {
        $parameters = [
            'participantId' => $participantId,
            'id' => $id,
        ];
        
        $qb = $this->createQueryBuilder('participantNote');
        $qb->select('participantNote')
                ->andWhere($qb->expr()->eq('participantNote.id', ':id'))
                ->leftJoin('participantNote.participant', 'participant')
                ->andWhere($qb->expr()->eq('participant.id', ':participantId'))
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

    public function aParticipantNoteInProgram(string $programId, string $id): ParticipantNote
    {
        $parameters = [
            'programId' => $programId,
            'id' => $id,
        ];
        
        $qb = $this->createQueryBuilder('participantNote');
        $qb->select('participantNote')
                ->andWhere($qb->expr()->eq('participantNote.id', ':id'))
                ->leftJoin('participantNote.note', 'note')
                ->andWhere($qb->expr()->eq('note.removed', 'false'))
                ->leftJoin('participantNote.participant', 'participant')
                ->leftJoin('participant.program', 'program')
                ->andWhere($qb->expr()->eq('program.id', ':programId'))
                ->setParameters($parameters)
                ->setMaxResults(1);
        
        try {
            return $qb->getQuery()->getSingleResult();
        } catch (NoResultException $ex) {
            throw RegularException::notFound('participant note not found');
        }
    }

}
