<?php

namespace Query\Infrastructure\Persistence\Doctrine\Repository;

use Doctrine\ORM\EntityRepository;
use Query\Domain\Task\Dependency\NoteFilter;
use Query\Domain\Task\Dependency\NoteFilterForConsultant;
use Query\Domain\Task\Dependency\NoteFilterForCoordinator;
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
    Note.name,
    Note.description,
    Note.modifiedTime,
    Note.createdTime,
                
    ConsultantNote.id consultantNoteId,
    CoordinatorNote.id coordinatorNoteId,
    CONCAT(Personnel.firstName, ' ', COALESCE(Personnel.lastName, '')) personnelName,

    ParticipantNote.id participantNoteId
FROM Note
    LEFT JOIN ConsultantNote ON ConsultantNote.Note_id = Note.id AND ConsultantNote.viewableByParticipant = true
    LEFT JOIN Consultant _aCt ON _aCt.id = ConsultantNote.Consultant_id
                
    LEFT JOIN CoordinatorNote ON CoordinatorNote.Note_id = Note.id AND CoordinatorNote.viewableByParticipant = true
    LEFT JOIN Coordinator _bCr ON _bCr.id = CoordinatorNote.Coordinator_id
                
    LEFT JOIN Personnel ON Personnel.id = _aCt.Personnel_id OR Personnel.id = _bCr.Personnel_id
                
    LEFT JOIN ParticipantNote ON ParticipantNote.Note_id = Note.id
                
    INNER JOIN Participant 
        ON Participant.id = ParticipantNote.Participant_id
        OR Participant.id = ConsultantNote.Participant_id
        OR Participant.id = CoordinatorNote.Participant_id
                
WHERE Note.removed = false
    AND Participant.id = :participantId
    {$filter->getOptionalConditionStatement($parameters)}

{$filter->getOrderStatement()}
{$filter->getLimitStatement()}
_STATEMENT;

        $query = $this->getEntityManager()->getConnection()->prepare($statement);
        return [
            'total' => $this->totalCountOfAllNoteAccessibleByParticipant($participantId, $filter),
            'list' => $query->executeQuery($parameters)->fetchAllAssociative(),
        ];
    }

    public function totalCountOfAllNoteAccessibleByParticipant(string $participantId, NoteFilter $filter)
    {
        $parameters = [
            'participantId' => $participantId
        ];

        $statement = <<<_STATEMENT
SELECT COUNT(*) total
FROM Note
    LEFT JOIN ConsultantNote ON ConsultantNote.Note_id = Note.id AND ConsultantNote.viewableByParticipant = true
    LEFT JOIN CoordinatorNote ON CoordinatorNote.Note_id = Note.id AND CoordinatorNote.viewableByParticipant = true
    LEFT JOIN ParticipantNote ON ParticipantNote.Note_id = Note.id
    INNER JOIN Participant 
        ON Participant.id = ParticipantNote.Participant_id
        OR Participant.id = ConsultantNote.Participant_id
        OR Participant.id = CoordinatorNote.Participant_id
WHERE Note.removed = false
    AND Participant.id = :participantId
    {$filter->getOptionalConditionStatement($parameters)}
_STATEMENT;

        $query = $this->getEntityManager()->getConnection()->prepare($statement);
        return $query->executeQuery($parameters)->fetchFirstColumn()[0];
    }

    public function allNotesInProgramsCoordinatedByPersonnel(string $personnelId, NoteFilterForCoordinator $filter)
    {
        $parameters = [
            'personnelId' => $personnelId,
        ];
        
        $statement = <<<_STATEMENT
SELECT
    Note.name,
    Note.description,
    Note.modifiedTime,
    Note.createdTime,

    ConsultantNote.id consultantNoteId,
    CoordinatorNote.id coordinatorNoteId,
    CONCAT(Personnel.firstName, ' ', COALESCE(Personnel.lastName, '')) personnelName,

    ParticipantNote.id participantNoteId,
    COALESCE(
        CONCAT(User.firstName, ' ', COALESCE(User.lastName, '')), 
        CONCAT(Client.firstName, ' ', COALESCE(Client.lastName, '')), 
        Team.name
    ) participantName,

    Coordinator.id coordinatorId,
    Program.name programName
FROM Note
                
    LEFT JOIN ConsultantNote ON ConsultantNote.Note_id = Note.id
    LEFT JOIN Consultant _ct ON _ct.id = ConsultantNote.Consultant_id
                
    LEFT JOIN CoordinatorNote ON CoordinatorNote.Note_id = Note.id
    LEFT JOIN Coordinator _cr ON _cr.id = CoordinatorNote.Coordinator_id
                
    LEFT JOIN Personnel ON Personnel.id = _cr.Personnel_id OR Personnel.id = _ct.Personnel_id
                
    LEFT JOIN ParticipantNote ON ParticipantNote.Note_id = Note.id
    INNER JOIN Participant 
        ON Participant.id = ParticipantNote.Participant_id
        OR Participant.id = ConsultantNote.Participant_id
        OR Participant.id = CoordinatorNote.Participant_id
    LEFT JOIN UserParticipant ON UserParticipant.Participant_id = Participant.id
    LEFT JOIN User ON User.id= UserParticipant.User_id
    LEFT JOIN ClientParticipant ON ClientParticipant.Participant_id = Participant.id
    LEFT JOIN Client ON Client.id = ClientParticipant.Client_id
    LEFT JOIN TeamParticipant ON TeamParticipant.Participant_id = Participant.id
    LEFT JOIN Team ON Team.id = TeamParticipant.Team_id
    
    INNER JOIN Coordinator 
        ON Coordinator.Program_id = Participant.Program_id 
        AND Coordinator.active = true 
        AND Coordinator.Personnel_id = :personnelId
                
    INNER JOIN Program ON Program.id = Coordinator.Program_id
                
WHERE Note.removed = false
    {$filter->getOptionalConditionStatement($parameters)}
    
{$filter->getOrderStatement()}
{$filter->getLimitStatement()}
_STATEMENT;
        
        $query = $this->getEntityManager()->getConnection()->prepare($statement);
        return [
            'total' => $this->totalNotesInProgramsCoordinatedByPersonnel($personnelId, $filter),
            'list' => $query->executeQuery($parameters)->fetchAllAssociative(),
        ];
    }
    protected function totalNotesInProgramsCoordinatedByPersonnel(string $personnelId, NoteFilterForCoordinator $filter)
    {
        $parameters = [
            'personnelId' => $personnelId,
        ];
        
        $statement = <<<_STATEMENT
SELECT COUNT(*) total
FROM Note
    LEFT JOIN ConsultantNote ON ConsultantNote.Note_id = Note.id
    LEFT JOIN CoordinatorNote ON CoordinatorNote.Note_id = Note.id
    LEFT JOIN ParticipantNote ON ParticipantNote.Note_id = Note.id
    INNER JOIN Participant 
        ON Participant.id = ParticipantNote.Participant_id
        OR Participant.id = ConsultantNote.Participant_id
        OR Participant.id = CoordinatorNote.Participant_id
    INNER JOIN Coordinator 
        ON Coordinator.Program_id = Participant.Program_id 
        AND Coordinator.active = true 
        AND Coordinator.Personnel_id = :personnelId
WHERE Note.removed = false
    {$filter->getOptionalConditionStatement($parameters)}
_STATEMENT;
        
        $query = $this->getEntityManager()->getConnection()->prepare($statement);
        return $query->executeQuery($parameters)->fetchFirstColumn()[0];
    }

    public function allNotesInProgramsMentoredByPersonnel(string $personnelId, NoteFilterForConsultant $filter)
    {
        $parameters = [
            'personnelId' => $personnelId
        ];
        
        $statement = <<<_STATEMENT
SELECT
    Note.name,
    Note.description,
    Note.modifiedTime,
    Note.createdTime,

    ConsultantNote.id consultantNoteId,
    CoordinatorNote.id coordinatorNoteId,
    ParticipantNote.id participantNoteId,
                
    CONCAT(Personnel.firstName, ' ', COALESCE(Personnel.lastName, '')) personnelName,
    COALESCE(
        CONCAT(User.firstName, ' ', COALESCE(User.lastName, '')), 
        CONCAT(Client.firstName, ' ', COALESCE(Client.lastName, '')), 
        Team.name
    ) participantName,
                
    DedicatedMentor.id dedicatedMenteeId,

    Consultant.id consultantId,
    Program.name programName
FROM
    Note
    LEFT JOIN ConsultantNote ON ConsultantNote.Note_id = Note.id
    LEFT JOIN Consultant _ct ON _ct.id = ConsultantNote.Consultant_id

    LEFT JOIN CoordinatorNote ON CoordinatorNote.Note_id = Note.id
    LEFT JOIN Coordinator _cr ON _cr.id = CoordinatorNote.Coordinator_id

    LEFT JOIN Personnel ON Personnel.id = _ct.Personnel_id OR Personnel.id = _cr.Personnel_id

    LEFT JOIN ParticipantNote ON ParticipantNote.Note_id = Note.id

    INNER JOIN Participant 
        ON Participant.id = ParticipantNote.Participant_id
        OR Participant.id = ConsultantNote.Participant_id
        OR Participant.id = CoordinatorNote.Participant_id
    LEFT JOIN UserParticipant ON UserParticipant.Participant_id = Participant.id
    LEFT JOIN User ON User.id= UserParticipant.User_id
    LEFT JOIN ClientParticipant ON ClientParticipant.Participant_id = Participant.id
    LEFT JOIN Client ON Client.id = ClientParticipant.Client_id
    LEFT JOIN TeamParticipant ON TeamParticipant.Participant_id = Participant.id
    LEFT JOIN Team ON Team.id = TeamParticipant.Team_id
    
    INNER JOIN Consultant
        ON Consultant.Program_id = Participant.Program_id
        AND Consultant.Personnel_id = :personnelId
        AND Consultant.active = true
    
    LEFT JOIN DedicatedMentor 
        ON DedicatedMentor.Consultant_id = Consultant.id
        AND DedicatedMentor.Participant_id = Participant.id
        AND DedicatedMentor.cancelled = false
                
    INNER JOIN Program ON Program.id = Participant.Program_id

WHERE
    Note.removed = false
    {$filter->getOptionalConditionStatement($parameters)}
{$filter->getOrderStatement()}
{$filter->getLimitStatement()}
_STATEMENT;
        
        $query = $this->getEntityManager()->getConnection()->prepare($statement);
        return [
            'total' => $this->totalNotesInProgramsMentoredByPersonnel($personnelId, $filter),
            'list' => $query->executeQuery($parameters)->fetchAllAssociative(),
        ];
    }
    protected function totalNotesInProgramsMentoredByPersonnel(string $personnelId, NoteFilterForConsultant $filter)
    {
        $parameters = [
            'personnelId' => $personnelId
        ];
        
        $statement = <<<_STATEMENT
SELECT COUNT(*) total
FROM
    Note
    LEFT JOIN ConsultantNote ON ConsultantNote.Note_id = Note.id

    LEFT JOIN CoordinatorNote ON CoordinatorNote.Note_id = Note.id

    LEFT JOIN ParticipantNote ON ParticipantNote.Note_id = Note.id

    INNER JOIN Participant 
        ON Participant.id = ParticipantNote.Participant_id
        OR Participant.id = ConsultantNote.Participant_id
        OR Participant.id = CoordinatorNote.Participant_id
                
    INNER JOIN Consultant
        ON Consultant.Program_id = Participant.Program_id
        AND Consultant.Personnel_id = :personnelId
        AND Consultant.active = true
    
    LEFT JOIN DedicatedMentor 
        ON DedicatedMentor.Consultant_id = Consultant.id
        AND DedicatedMentor.Participant_id = Participant.id
        AND DedicatedMentor.cancelled = false

WHERE
    Note.removed = false
    {$filter->getOptionalConditionStatement($parameters)}
_STATEMENT;
        
        $query = $this->getEntityManager()->getConnection()->prepare($statement);
        return $query->executeQuery($parameters)->fetchFirstColumn()[0];
    }

}
