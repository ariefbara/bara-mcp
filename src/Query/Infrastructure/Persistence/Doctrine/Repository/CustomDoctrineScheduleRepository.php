<?php

namespace Query\Infrastructure\Persistence\Doctrine\Repository;

use Doctrine\ORM\EntityManager;
use Query\Domain\Task\Dependency\ScheduleFilter;
use Query\Domain\Task\Dependency\ScheduleRepository;

class CustomDoctrineScheduleRepository implements ScheduleRepository
{

    /**
     * 
     * @var EntityManager
     */
    protected $em;

    public function __construct(EntityManager $em)
    {
        $this->em = $em;
    }

    public function allScheduleBelongsToClient(string $clientId, ScheduleFilter $filter): array
    {
        $parameters = ["clientId" => $clientId];
        $statement = <<<_STATEMENT
SELECT
    startTime,
    teamId,
    participantId,
    bookedMentoringSlotId,
    negotiatedMentoringId,
    mentorName,
    invitationId,
    agendaType,
    agendaName
FROM (
    SELECT
        MentoringSlot.startTime startTime,
        T_Member.Team_id teamId,
        Participant.id participantId,
        BookedMentoringSlot.id bookedMentoringSlotId,
        null as negotiatedMentoringId,
        CONCAT(Personnel.firstName, ' ', COALESCE(Personnel.lastName, '')) mentorName,
        null as invitationId,
        null as agendaType,
        null as agendaName
    FROM BookedMentoringSlot
        LEFT JOIN MentoringSlot ON MentoringSlot.id = BookedMentoringSlot.MentoringSlot_id
        LEFT JOIN Consultant ON Consultant.id = MentoringSlot.Mentor_id
        LEFT JOIN Personnel ON Personnel.id = Consultant.Personnel_id
        LEFT JOIN Participant ON Participant.id = BookedMentoringSlot.Participant_id
        LEFT JOIN ClientParticipant ON Participant.id = ClientParticipant.Participant_id
        LEFT JOIN TeamParticipant ON Participant.id = TeamParticipant.Participant_id
        LEFT JOIN T_Member ON T_Member.Team_id = TeamParticipant.Team_id
    WHERE (Participant.active = true
            AND (ClientParticipant.Client_id = :clientId OR (T_Member.Client_id = :clientId AND T_Member.active = true))
        )
        AND BookedMentoringSlot.cancelled = false
        {$filter->getSqlFromClause('MentoringSlot.startTime', $parameters)}
        {$filter->getSqlToClause('MentoringSlot.startTime', $parameters)}
        
    UNION
    SELECT
        MentoringRequest.startTime startTime,
        T_Member.Team_id teamId,
        Participant.id participantId,
        null as bookedMentoringSlotId,
        NegotiatedMentoring.id negotiatedMentoringId,
        CONCAT(Personnel.firstName, ' ', COALESCE(Personnel.lastName, '')) mentorName,
        null as invitationId,
        null as agendaType,
        null as agendaName
    FROM NegotiatedMentoring
        LEFT JOIN MentoringRequest ON MentoringRequest.id = NegotiatedMentoring.MentoringRequest_id
        LEFT JOIN Consultant ON Consultant.id = MentoringRequest.Consultant_id
        LEFT JOIN Personnel ON Personnel.id = Consultant.Personnel_id
        LEFT JOIN Participant ON Participant.id = MentoringRequest.Participant_id
        LEFT JOIN ClientParticipant ON Participant.id = ClientParticipant.Participant_id
        LEFT JOIN TeamParticipant ON Participant.id = TeamParticipant.Participant_id
        LEFT JOIN T_Member ON T_Member.Team_id = TeamParticipant.Team_id
    WHERE (Participant.active = true
            AND (ClientParticipant.Client_id = :clientId OR (T_Member.Client_id = :clientId AND T_Member.active = true))
        )
        {$filter->getSqlFromClause('MentoringRequest.startTime', $parameters)}
        {$filter->getSqlToClause('MentoringRequest.startTime', $parameters)}
                
    UNION
    SELECT
        Activity.startDateTime startTime,
        T_Member.Team_id teamId,
        Participant.id participantId,
        null as bookedMentoringSlotId,
        null as mentoringId,
        null as mentorName,
        ParticipantInvitee.id invitationId,
        ActivityType.name agendaType,
        Activity.name agendaName
    FROM ParticipantInvitee
        LEFT JOIN Invitee ON Invitee.id = ParticipantInvitee.Invitee_id
        LEFT JOIN Activity ON Activity.id = Invitee.Activity_id
        LEFT JOIN ActivityType ON ActivityType.id = Activity.ActivityType_id
        LEFT JOIN Participant ON Participant.id = ParticipantInvitee.Participant_id
        LEFT JOIN ClientParticipant ON Participant.id = ClientParticipant.Participant_id
        LEFT JOIN TeamParticipant ON Participant.id = TeamParticipant.Participant_id
        LEFT JOIN T_Member ON T_Member.Team_id = TeamParticipant.Team_id
    WHERE (Participant.active = true
            AND (ClientParticipant.Client_id = :clientId OR (T_Member.Client_id = :clientId AND T_Member.active = true))
        )
        AND Invitee.cancelled = false
        {$filter->getSqlFromClause('Activity.startDateTime', $parameters)}
        {$filter->getSqlToClause('Activity.startDateTime', $parameters)}
)_a
ORDER BY startTime {$filter->getOrderDirection()}
LIMIT {$filter->getOffset()}, {$filter->getPageSize()}
_STATEMENT;
        $query = $this->em->getConnection()->prepare($statement);
        return [
            'total' => $this->totalCountOfAllScheduleBelongsToClient($clientId, $filter),
            'list' => $query->executeQuery($parameters)->fetchAllAssociative(),
        ];
    }
    
    protected function totalCountOfAllScheduleBelongsToClient(string $clientId, ScheduleFilter $filter): ?int
    {
        $parameters = ["clientId" => $clientId];
        $statement = <<<_STATEMENT
SELECT COUNT(*) total
FROM (
    SELECT
        BookedMentoringSlot.id bookedMentoringSlotId,
        null as negotiatedMentoringId,
        null as invitationId
    FROM BookedMentoringSlot
        LEFT JOIN MentoringSlot ON MentoringSlot.id = BookedMentoringSlot.MentoringSlot_id
    WHERE BookedMentoringSlot.Participant_id IN (
        SELECT Participant.id
        FROM Participant
            LEFT JOIN ClientParticipant ON ClientParticipant.Participant_id = Participant.id
            LEFT JOIN TeamParticipant ON TeamParticipant.Participant_id = Participant.id
            LEFT JOIN T_Member ON T_Member.Team_id = TeamParticipant.Team_id
        WHERE Participant.active = true
            AND (ClientParticipant.Client_id = :clientId OR (T_Member.Client_id = :clientId AND T_Member.active = true))
    )
        AND BookedMentoringSlot.cancelled = false
        {$filter->getSqlFromClause('MentoringSlot.startTime', $parameters)}
        {$filter->getSqlToClause('MentoringSlot.startTime', $parameters)}
        
    UNION
    SELECT
        null as bookedMentoringSlotId,
        NegotiatedMentoring.id negotiatedMentoringId,
        null as invitationId
    FROM NegotiatedMentoring
        LEFT JOIN MentoringRequest ON MentoringRequest.id = NegotiatedMentoring.MentoringRequest_id
    WHERE MentoringRequest.Participant_id IN (
        SELECT Participant.id
        FROM Participant
            LEFT JOIN ClientParticipant ON ClientParticipant.Participant_id = Participant.id
            LEFT JOIN TeamParticipant ON TeamParticipant.Participant_id = Participant.id
            LEFT JOIN T_Member ON T_Member.Team_id = TeamParticipant.Team_id
        WHERE Participant.active = true
            AND (ClientParticipant.Client_id = :clientId OR (T_Member.Client_id = :clientId AND T_Member.active = true))
    )
        {$filter->getSqlFromClause('MentoringRequest.startTime', $parameters)}
        {$filter->getSqlToClause('MentoringRequest.startTime', $parameters)}
                
    UNION
    SELECT
        null as bookedMentoringSlotId,
        null as mentoringId,
        ParticipantInvitee.id invitationId
    FROM ParticipantInvitee
        LEFT JOIN Invitee ON Invitee.id = ParticipantInvitee.Invitee_id
        LEFT JOIN Activity ON Activity.id = Invitee.Activity_id
    WHERE ParticipantInvitee.Participant_id IN (
        SELECT Participant.id
        FROM Participant
            LEFT JOIN ClientParticipant ON ClientParticipant.Participant_id = Participant.id
            LEFT JOIN TeamParticipant ON TeamParticipant.Participant_id = Participant.id
            LEFT JOIN T_Member ON T_Member.Team_id = TeamParticipant.Team_id
        WHERE Participant.active = true
            AND (ClientParticipant.Client_id = :clientId OR (T_Member.Client_id = :clientId AND T_Member.active = true))
    )
        AND Invitee.cancelled = false
        {$filter->getSqlFromClause('Activity.startDateTime', $parameters)}
        {$filter->getSqlToClause('Activity.startDateTime', $parameters)}
)_a
_STATEMENT;
        $query = $this->em->getConnection()->prepare($statement);
        return $query->executeQuery($parameters)->fetchFirstColumn()[0];
    }

    public function allScheduleBelongsToParticipant(string $participantId, ScheduleFilter $filter): array
    {
        $parameters = ["participantId" => $participantId];
        $statement = <<<_STATEMENT
SELECT
    startTime,
    bookedMentoringSlotId,
    negotiatedMentoringId,
    mentorName,
    invitationId,
    agendaType,
    agendaName
FROM (
    SELECT
        MentoringSlot.startTime startTime,
        BookedMentoringSlot.id bookedMentoringSlotId,
        null as negotiatedMentoringId,
        CONCAT(Personnel.firstName, ' ', COALESCE(Personnel.lastName, '')) mentorName,
        null as invitationId,
        null as agendaType,
        null as agendaName
    FROM BookedMentoringSlot
        LEFT JOIN MentoringSlot ON MentoringSlot.id = BookedMentoringSlot.MentoringSlot_id
        LEFT JOIN Consultant ON Consultant.id = MentoringSlot.Mentor_id
        LEFT JOIN Personnel ON Personnel.id = Consultant.Personnel_id
    WHERE BookedMentoringSlot.Participant_id = :participantId
        AND BookedMentoringSlot.cancelled = false
        {$filter->getSqlFromClause('MentoringSlot.startTime', $parameters)}
        {$filter->getSqlToClause('MentoringSlot.startTime', $parameters)}
        
    UNION
    SELECT
        MentoringRequest.startTime startTime,
        null as bookedMentoringSlotId,
        NegotiatedMentoring.id negotiatedMentoringId,
        CONCAT(Personnel.firstName, ' ', COALESCE(Personnel.lastName, '')) mentorName,
        null as invitationId,
        null as agendaType,
        null as agendaName
    FROM NegotiatedMentoring
        LEFT JOIN MentoringRequest ON MentoringRequest.id = NegotiatedMentoring.MentoringRequest_id
        LEFT JOIN Consultant ON Consultant.id = MentoringRequest.Consultant_id
        LEFT JOIN Personnel ON Personnel.id = Consultant.Personnel_id
    WHERE MentoringRequest.Participant_id = :participantId
        {$filter->getSqlFromClause('MentoringRequest.startTime', $parameters)}
        {$filter->getSqlToClause('MentoringRequest.startTime', $parameters)}
                
    UNION
    SELECT
        Activity.startDateTime startTime,
        null as bookedMentoringSlotId,
        null as mentoringId,
        null as mentorName,
        ParticipantInvitee.id invitationId,
        ActivityType.name agendaType,
        Activity.name agendaName
    FROM ParticipantInvitee
        LEFT JOIN Invitee ON Invitee.id = ParticipantInvitee.Invitee_id
        LEFT JOIN Activity ON Activity.id = Invitee.Activity_id
        LEFT JOIN ActivityType ON ActivityType.id = Activity.ActivityType_id
    WHERE ParticipantInvitee.Participant_id = :participantId
        AND Invitee.cancelled = false
        {$filter->getSqlFromClause('Activity.startDateTime', $parameters)}
        {$filter->getSqlToClause('Activity.startDateTime', $parameters)}
)_a
ORDER BY startTime {$filter->getOrderDirection()}
LIMIT {$filter->getOffset()}, {$filter->getPageSize()}
_STATEMENT;
        $query = $this->em->getConnection()->prepare($statement);
        return [
            'total' => $this->totalScheduleBelongsToParticipant($participantId, $filter),
            'list' => $query->executeQuery($parameters)->fetchAllAssociative(),
        ];
    }
    
    public function totalScheduleBelongsToParticipant(string $participantId, ScheduleFilter $filter): ?int
    {
        $parameters = ["participantId" => $participantId];
        $statement = <<<_STATEMENT
SELECT COUNT(*) total
FROM (
    SELECT
        BookedMentoringSlot.id bookedMentoringSlotId,
        null as negotiatedMentoringId,
        null as invitationId
    FROM BookedMentoringSlot
        LEFT JOIN MentoringSlot ON MentoringSlot.id = BookedMentoringSlot.MentoringSlot_id
    WHERE BookedMentoringSlot.Participant_id = :participantId
        AND BookedMentoringSlot.cancelled = false
        {$filter->getSqlFromClause('MentoringSlot.startTime', $parameters)}
        {$filter->getSqlToClause('MentoringSlot.startTime', $parameters)}
        
    UNION
    SELECT
        null as bookedMentoringSlotId,
        NegotiatedMentoring.id negotiatedMentoringId,
        null as invitationId
    FROM NegotiatedMentoring
        LEFT JOIN MentoringRequest ON MentoringRequest.id = NegotiatedMentoring.MentoringRequest_id
    WHERE MentoringRequest.Participant_id = :participantId
        {$filter->getSqlFromClause('MentoringRequest.startTime', $parameters)}
        {$filter->getSqlToClause('MentoringRequest.startTime', $parameters)}
                
    UNION
    SELECT
        null as bookedMentoringSlotId,
        null as mentoringId,
        ParticipantInvitee.id invitationId
    FROM ParticipantInvitee
        LEFT JOIN Invitee ON Invitee.id = ParticipantInvitee.Invitee_id
        LEFT JOIN Activity ON Activity.id = Invitee.Activity_id
    WHERE ParticipantInvitee.Participant_id = :participantId
        AND Invitee.cancelled = false
        {$filter->getSqlFromClause('Activity.startDateTime', $parameters)}
        {$filter->getSqlToClause('Activity.startDateTime', $parameters)}
)_a
_STATEMENT;
        $query = $this->em->getConnection()->prepare($statement);
        return $query->executeQuery($parameters)->fetchFirstColumn()[0];
    }

}
