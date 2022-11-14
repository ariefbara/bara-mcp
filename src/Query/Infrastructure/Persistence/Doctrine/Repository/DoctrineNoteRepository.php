<?php

namespace Query\Infrastructure\Persistence\Doctrine\Repository;

use Doctrine\ORM\EntityRepository;
use Query\Domain\Task\Dependency\NoteFilter;
use Query\Domain\Task\Dependency\NoteRepository;

class DoctrineNoteRepository extends EntityRepository implements NoteRepository
{

    public function allNoteAccessibleByParticipant(string $participantId, NoteFilter $filter)
    {
        $parameters = [
            'participantId' => $participantId
        ];

        $statement = <<<_STATEMENT
SELECT 
    Note.content,
    Note.createdTime,
    Note.modifiedTime,
    ParticipantNote.id participantNoteId,
    ConsultantNote.id consultantNoteId,
    Consultant.id consultantId,
    CONCAT(_pCons.firstName, ' ', COALESCE(_pCons.lastName, '')) consultantName,
    CoordinatorNote.id coordinatorNoteId,
    Coordinator.id coordinatorId,
    CONCAT(_pCoor.firstName, ' ', COALESCE(_pCoor.lastName, '')) coordinatorName
FROM Note
LEFT JOIN ParticipantNote
    ON ParticipantNote.Note_id = Note.id AND ParticipantNote.Participant_id = :participantId
LEFT JOIN ConsultantNote
    ON ConsultantNote.Note_id = Note.id AND ConsultantNote.Participant_id = :participantId AND ConsultantNote.viewableByParticipant = true
LEFT JOIN Consultant
    ON Consultant.id = ConsultantNote.Consultant_id
LEFT JOIN Personnel _pCons
    ON _pCons.id = Consultant.Personnel_id
LEFT JOIN CoordinatorNote
    ON CoordinatorNote.Note_id = Note.id AND CoordinatorNote.Participant_id = :participantId AND CoordinatorNote.viewableByParticipant = true
LEFT JOIN Coordinator
    ON Coordinator.id = CoordinatorNote.Coordinator_id
LEFT JOIN Personnel _pCoor 
    ON _pCoor.id = Coordinator.Personnel_id
WHERE Note.removed = false
    AND (ParticipantNote.id IS NOT NULL OR ConsultantNote.id IS NOT NULL OR CoordinatorNote.id IS NOT NULL)
{$filter->getOrderStatement('modifiedTime', 'createdTime')}
LIMIT {$filter->getPaginationFilter()->getOffset()}, {$filter->getPaginationFilter()->getPageSize()}
_STATEMENT;

        $query = $this->getEntityManager()->getConnection()->prepare($statement);
        return [
            'total' => $this->totalCountOfAllNoteAccessibleByParticipant($participantId),
            'list' => $query->executeQuery($parameters)->fetchAllAssociative(),
        ];
    }

    public function totalCountOfAllNoteAccessibleByParticipant(string $participantId)
    {
        $parameters = [
            'participantId' => $participantId
        ];

        $statement = <<<_STATEMENT
SELECT COUNT(*) total
FROM Note
LEFT JOIN ParticipantNote
    ON ParticipantNote.Note_id = Note.id AND ParticipantNote.Participant_id = :participantId
LEFT JOIN ConsultantNote
    ON ConsultantNote.Note_id = Note.id AND ConsultantNote.Participant_id = :participantId AND ConsultantNote.viewableByParticipant = true
LEFT JOIN CoordinatorNote
    ON CoordinatorNote.Note_id = Note.id AND CoordinatorNote.Participant_id = :participantId AND CoordinatorNote.viewableByParticipant = true
WHERE Note.removed = false
    AND (ParticipantNote.id IS NOT NULL OR ConsultantNote.id IS NOT NULL OR CoordinatorNote.id IS NOT NULL)
_STATEMENT;

        $query = $this->getEntityManager()->getConnection()->prepare($statement);
        return $query->executeQuery($parameters)->fetchFirstColumn()[0];
    }

}
