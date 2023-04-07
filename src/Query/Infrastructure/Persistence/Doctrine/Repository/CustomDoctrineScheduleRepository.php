<?php

namespace Query\Infrastructure\Persistence\Doctrine\Repository;

use Doctrine\ORM\EntityManager;
use Query\Domain\Task\Dependency\ScheduleFilter;
use Query\Domain\Task\Dependency\ScheduleRepository;
use SharedContext\Domain\ValueObject\DeclaredMentoringStatus;

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
    startTime,
    teamId,
    programId,
    programType,
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
        Program.id programId,
        Program.programType programType,
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
        LEFT JOIN Program ON Program.id = Participant.Program_id
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
        Program.id programId,
        Program.programType programType,
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
        LEFT JOIN Program ON Program.id = Participant.Program_id
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
        Program.id programId,
        Program.programType programType,
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
        LEFT JOIN Program ON Program.id = Participant.Program_id
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
    teamId,
    programId,
    programType,
    bookedMentoringSlotId,
    negotiatedMentoringId,
    mentorName,
    invitationId,
    agendaType,
    agendaName
FROM (
    SELECT
        MentoringSlot.startTime startTime,
        TeamParticipant.Team_id teamId,
        Program.id programId,
        Program.programType programType,
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
        LEFT JOIN Program ON Program.id = Consultant.Program_id
        LEFT JOIN TeamParticipant ON BookedMentoringSlot.Participant_id = TeamParticipant.Participant_id
    WHERE BookedMentoringSlot.Participant_id = :participantId
        AND BookedMentoringSlot.cancelled = false
        {$filter->getSqlFromClause('MentoringSlot.startTime', $parameters)}
        {$filter->getSqlToClause('MentoringSlot.startTime', $parameters)}
        
    UNION
    SELECT
        MentoringRequest.startTime startTime,
        TeamParticipant.Team_id teamId,
        Program.id programId,
        Program.programType programType,
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
        LEFT JOIN Program ON Program.id = Consultant.Program_id
        LEFT JOIN TeamParticipant ON MentoringRequest.Participant_id = TeamParticipant.Participant_id
    WHERE MentoringRequest.Participant_id = :participantId
        {$filter->getSqlFromClause('MentoringRequest.startTime', $parameters)}
        {$filter->getSqlToClause('MentoringRequest.startTime', $parameters)}
                
    UNION
    SELECT
        Activity.startDateTime startTime,
        TeamParticipant.Team_id teamId,
        Program.id programId,
        Program.programType programType,
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
        LEFT JOIN Program ON Program.id = ActivityType.Program_id
        LEFT JOIN TeamParticipant ON ParticipantInvitee.Participant_id = TeamParticipant.Participant_id
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

    public function allScheduleInProgram(string $programId, ScheduleFilter $filter): array
    {
        $parameters = ["programId" => $programId];
        $activeDeclaredStatus = implode(' ,', [
            DeclaredMentoringStatus::APPROVED_BY_MENTOR,
            DeclaredMentoringStatus::APPROVED_BY_PARTICIPANT,
            DeclaredMentoringStatus::DECLARED_BY_MENTOR,
            DeclaredMentoringStatus::DECLARED_BY_PARTICIPANT,
        ]);
        $statement = <<<_STATEMENT
SELECT
    _a.mentoringSlotId,
    _a.mentoringSlotCapacity,
    _a.bookedSlotCount,
    _a.bookedParticipants,

    _a.negotiatedMentoringId,
    _a.declaredMentoringId,
    _a.consultantId,
    CONCAT(Personnel.firstName, ' ', COALESCE(Personnel.lastName, '')) personnelName,
    _a.participantId,
    COALESCE(
        CONCAT(User.firstName, ' ', COALESCE(User.lastName, '')), 
        CONCAT(Client.firstName, ' ', COALESCE(Client.lastName, '')), 
        Team.name
    ) participantName,

    _a.consultationSetupName,

    _a.startTime,
    _a.endTime,
    _a.mediaType,
    _a.location,

    _a.activityId,
    _a.activityName,
    _a.activityDescription,
    _a.activityNote,
    _a.invitationCount,
    _a.inviteeList
    -- 
FROM (
    SELECT
        MentoringSlot.id mentoringSlotId,
        MentoringSlot.capacity mentoringSlotCapacity,
        _a1.bookedSlotCount,
        _a1.bookedParticipants,
        null negotiatedMentoringId,
        null declaredMentoringId,
        null activityId,

        ConsultationSetup.name consultationSetupName,
        MentoringSlot.Mentor_id consultantId,
        null participantId,

        MentoringSlot.startTime,
        MentoringSlot.endTime,
        MentoringSlot.mediaType,
        MentoringSlot.location,

        null activityName,
        null activityDescription,
        null activityNote,
        null invitationCount,
        null inviteeList

    FROM MentoringSlot
        INNER JOIN ConsultationSetup ON ConsultationSetup.id = MentoringSlot.ConsultationSetup_id AND ConsultationSetup.Program_id = :programId
        LEFT JOIN (
            SELECT 
                BookedMentoringSlot.MentoringSlot_id mentoringSlotId,
                COUNT(BookedMentoringSlot.id) bookedSlotCount,
                GROUP_CONCAT(
                    COALESCE(
                        CONCAT(User.firstName, ' ', COALESCE(User.lastName, '')), 
                        CONCAT(Client.firstName, ' ', COALESCE(Client.lastName, '')), 
                        Team.name
                    )
                    SEPARATOR ', '
                ) as bookedParticipants
            FROM BookedMentoringSlot
                INNER JOIN Participant ON Participant.id = BookedMentoringSlot.Participant_id
                -- 
                LEFT JOIN UserParticipant ON UserParticipant.Participant_id = Participant.id
                LEFT JOIN User ON User.id= UserParticipant.User_id
                LEFT JOIN ClientParticipant ON ClientParticipant.Participant_id = Participant.id
                LEFT JOIN Client ON Client.id = ClientParticipant.Client_id
                LEFT JOIN TeamParticipant ON TeamParticipant.Participant_id = Participant.id
                LEFT JOIN Team ON Team.id = TeamParticipant.Team_id
            WHERE BookedMentoringSlot.cancelled = false
            GROUP BY mentoringSlotId
        )_a1 ON _a1.mentoringSlotId = MentoringSlot.id
    WHERE MentoringSlot.cancelled = false
        {$filter->getSqlFromClause('MentoringSlot.startTime', $parameters)}
        {$filter->getSqlToClause('MentoringSlot.startTime', $parameters)}
        
    UNION
    SELECT
        null mentoringSlotId,
        null mentoringSlotCapacity,
        null bookedSlotCount,
        null bookedParticipants,
        NegotiatedMentoring.id negotiatedMentoringId,
        null declaredMentoringId,
        null activityId,

        ConsultationSetup.name consultationSetupName,
        MentoringRequest.Consultant_id consultantId,
        MentoringRequest.Participant_id participantId,

        MentoringRequest.startTime,
        MentoringRequest.endTime,
        MentoringRequest.mediaType,
        MentoringRequest.location,

        null activityName,
        null activityDescription,
        null activityNote,
        null invitationCount,
        null inviteeList

    FROM NegotiatedMentoring
        INNER JOIN MentoringRequest ON MentoringRequest.id = NegotiatedMentoring.MentoringRequest_id
        INNER JOIN ConsultationSetup ON ConsultationSetup.id = MentoringRequest.ConsultationSetup_id AND ConsultationSetup.Program_id = :programId
    WHERE 1
        {$filter->getSqlFromClause('MentoringRequest.startTime', $parameters)}
        {$filter->getSqlToClause('MentoringRequest.startTime', $parameters)}

    UNION
    SELECT
        null mentoringSlotId,
        null mentoringSlotCapacity,
        null bookedSlotCount,
        null bookedParticipants,
        null negotiatedMentoringId,
        DeclaredMentoring.id declaredMentoringId,
        null activityId,
        -- 
        ConsultationSetup.name consultationSetupName,
        DeclaredMentoring.Consultant_id consultantId,
        DeclaredMentoring.Participant_id participantId,
        -- 
        DeclaredMentoring.startTime,
        DeclaredMentoring.endTime,
        DeclaredMentoring.mediaType,
        DeclaredMentoring.location,
        -- 
        null activityName,
        null activityDescription,
        null activityNote,
        null invitationCount,
        null inviteeList
        -- 
    FROM DeclaredMentoring
        INNER JOIN ConsultationSetup ON ConsultationSetup.id = DeclaredMentoring.ConsultationSetup_id AND ConsultationSetup.Program_id = :programId
        INNER JOIN Consultant ON Consultant.id = DeclaredMentoring.Consultant_id
        INNER JOIN Participant ON Participant.id = DeclaredMentoring.Participant_id
    WHERE DeclaredMentoring.declaredStatus IN ({$activeDeclaredStatus})
        {$filter->getSqlFromClause('DeclaredMentoring.startTime', $parameters)}
        {$filter->getSqlToClause('DeclaredMentoring.startTime', $parameters)}

    UNION
    SELECT 
        null mentoringSlotId,
        null mentoringSlotCapacity,
        null bookedSlotCount,
        null bookedParticipants,
        null negotiatedMentoringId,
        null declaredMentoringId,
        Activity.id activityId,

        null consultationSetupName,
        null consultantId,
        null participantId,

        Activity.startDateTime startTime,
        Activity.endDateTime endTime,
        null mediaType,
        Activity.location,

        Activity.name activityName,
        Activity.description activityDescription,
        Activity.note activityNote,
        _a4.invitationCount,
        _a4.inviteeList

    FROM Activity
        INNER JOIN ActivityType ON ActivityType.id = Activity.ActivityType_id AND ActivityType.Program_id = :programId
        LEFT JOIN (
            SELECT Invitee.Activity_id activityId,
                COUNT(Invitee.id) invitationCount,
                GROUP_CONCAT(
                    COALESCE(
                        CONCAT(User.firstName, ' ', COALESCE(User.lastName, ''), ' [user-participant]', CASE WHEN Invitee.anInitiator THEN ' [initiator]' ELSE '' END), 
                        CONCAT(Client.firstName, ' ', COALESCE(Client.lastName, ''), ' [client-participant]', CASE WHEN Invitee.anInitiator THEN ' [initiator]' ELSE '' END), 
                        CONCAT(Team.name, ' [team-participant]', CASE WHEN Invitee.anInitiator THEN ' [initiator]' ELSE '' END),
                        CONCAT(Coordinator_Personnel.firstName, ' ', COALESCE(Coordinator_Personnel.lastName, ''), ' [coordinator]', CASE WHEN Invitee.anInitiator THEN ' [initiator]' ELSE '' END), 
                        CONCAT(Consultant_Personnel.firstName, ' ', COALESCE(Consultant_Personnel.lastName, ''), ' [consultant]', CASE WHEN Invitee.anInitiator THEN ' [initiator]' ELSE '' END), 
                        CONCAT(Consultant_Personnel.firstName, ' ', COALESCE(Consultant_Personnel.lastName, ''), ' [consultant]', CASE WHEN Invitee.anInitiator THEN ' [initiator]' ELSE '' END), 
                        CONCAT(Manager.name, ' [manager]')
                    )
                    SEPARATOR ', '
                ) inviteeList
            FROM Invitee
                LEFT JOIN CoordinatorInvitee ON CoordinatorInvitee.Invitee_id = Invitee.id
                LEFT JOIN Coordinator ON Coordinator.id = CoordinatorInvitee.Coordinator_id
                LEFT JOIN Personnel AS Coordinator_Personnel ON Coordinator_Personnel.id = Coordinator.Personnel_id

                LEFT JOIN ConsultantInvitee ON ConsultantInvitee.Invitee_id = Invitee.id
                LEFT JOIN Consultant ON Consultant.id = ConsultantInvitee.Consultant_id
                LEFT JOIN Personnel AS Consultant_Personnel ON Consultant_Personnel.id = Consultant.Personnel_id

                LEFT JOIN ManagerInvitee ON ManagerInvitee.Invitee_id = Invitee.id
                LEFT JOIN Manager ON Manager.id = ManagerInvitee.Manager_id

                LEFT JOIN ParticipantInvitee ON ParticipantInvitee.Invitee_id = Invitee.id
                LEFT JOIN UserParticipant ON UserParticipant.Participant_id = ParticipantInvitee.Participant_id
                LEFT JOIN User ON User.id= UserParticipant.User_id
                LEFT JOIN ClientParticipant ON ClientParticipant.Participant_id = ParticipantInvitee.Participant_id
                LEFT JOIN Client ON Client.id = ClientParticipant.Client_id
                LEFT JOIN TeamParticipant ON TeamParticipant.Participant_id = ParticipantInvitee.Participant_id
                LEFT JOIN Team ON Team.id = TeamParticipant.Team_id

            WHERE Invitee.cancelled = false
            GROUP BY activityId
        )_a4 ON _a4.activityId = Activity.id
    WHERE Activity.cancelled = false
        {$filter->getSqlFromClause('Activity.startDateTime', $parameters)}
        {$filter->getSqlToClause('Activity.startDateTime', $parameters)}
)_a
    LEFT JOIN Consultant ON Consultant.id = _a.consultantId
    LEFT JOIN Personnel ON Personnel.id = Consultant.Personnel_id
    -- 
    LEFT JOIN UserParticipant ON UserParticipant.Participant_id = _a.participantId
    LEFT JOIN User ON User.id= UserParticipant.User_id
    LEFT JOIN ClientParticipant ON ClientParticipant.Participant_id = _a.participantId
    LEFT JOIN Client ON Client.id = ClientParticipant.Client_id
    LEFT JOIN TeamParticipant ON TeamParticipant.Participant_id = _a.participantId
    LEFT JOIN Team ON Team.id = TeamParticipant.Team_id
    -- 
ORDER BY startTime {$filter->getOrderDirection()}
_STATEMENT;
        $query = $this->em->getConnection()->prepare($statement);
        return $query->executeQuery($parameters)->fetchAllAssociative();
    }

    public function allScheduleBelongsToPersonnel(string $personnelId, ScheduleFilter $filter): array
    {
        $parameters = ["personnelId" => $personnelId];
        $activeDeclaredStatus = implode(' ,', [
            DeclaredMentoringStatus::APPROVED_BY_MENTOR,
            DeclaredMentoringStatus::APPROVED_BY_PARTICIPANT,
            DeclaredMentoringStatus::DECLARED_BY_MENTOR,
            DeclaredMentoringStatus::DECLARED_BY_PARTICIPANT,
        ]);
        $statement = <<<_STATEMENT
SELECT
    _a.mentoringSlotId,
    _a.mentoringSlotCapacity,
    _a.bookedSlotCount,
    _a.bookedParticipants,

    _a.negotiatedMentoringId,
    _a.declaredMentoringId,
    _a.participantId,
    COALESCE(
        CONCAT(User.firstName, ' ', COALESCE(User.lastName, '')), 
        CONCAT(Client.firstName, ' ', COALESCE(Client.lastName, '')), 
        Team.name
    ) participantName,
    --      
    _a.startTime,
    _a.endTime,
    _a.mediaType,
    _a.location,
    --      
    _a.consultantInviteeId,
    _a.coordinatorInviteeId,
    _a.activityId,
    _a.activityName,
    _a.activityDescription,
    _a.activityNote,
    _a.invitationCount,
    _a.inviteeList,
    --      
    _a.consultationSetupName,
    _a.consultantId,
    _a.coordinatorId,
    _a.programId,
    Program.name programName
    
    -- 
FROM (
    SELECT
        MentoringSlot.id mentoringSlotId,
        MentoringSlot.capacity mentoringSlotCapacity,
        _a1.bookedSlotCount,
        _a1.bookedParticipants,
        null negotiatedMentoringId,
        null declaredMentoringId,
        null activityId,
        --
        MentoringSlot.startTime,
        MentoringSlot.endTime,
        MentoringSlot.mediaType,
        MentoringSlot.location,
        --
        null consultantInviteeId,
        null coordinatorInviteeId,
        null activityName,
        null activityDescription,
        null activityNote,
        null invitationCount,
        null inviteeList,
        --
        ConsultationSetup.name consultationSetupName,
        MentoringSlot.Mentor_id consultantId,
        null coordinatorId,
        null participantId,
        Consultant.Program_id programId
        --
    FROM MentoringSlot
        INNER JOIN Consultant ON Consultant.id = MentoringSlot.Mentor_id AND Consultant.active = true AND Consultant.Personnel_id = :personnelId
        INNER JOIN ConsultationSetup ON ConsultationSetup.id = MentoringSlot.ConsultationSetup_id
        LEFT JOIN (
            SELECT 
                BookedMentoringSlot.MentoringSlot_id mentoringSlotId,
                COUNT(BookedMentoringSlot.id) bookedSlotCount,
                GROUP_CONCAT(
                    COALESCE(
                        CONCAT(User.firstName, ' ', COALESCE(User.lastName, '')), 
                        CONCAT(Client.firstName, ' ', COALESCE(Client.lastName, '')), 
                        Team.name
                    )
                    SEPARATOR ', '
                ) as bookedParticipants
            FROM BookedMentoringSlot
                INNER JOIN Participant ON Participant.id = BookedMentoringSlot.Participant_id
                -- 
                LEFT JOIN UserParticipant ON UserParticipant.Participant_id = Participant.id
                LEFT JOIN User ON User.id= UserParticipant.User_id
                LEFT JOIN ClientParticipant ON ClientParticipant.Participant_id = Participant.id
                LEFT JOIN Client ON Client.id = ClientParticipant.Client_id
                LEFT JOIN TeamParticipant ON TeamParticipant.Participant_id = Participant.id
                LEFT JOIN Team ON Team.id = TeamParticipant.Team_id
            WHERE BookedMentoringSlot.cancelled = false
            GROUP BY mentoringSlotId
        )_a1 ON _a1.mentoringSlotId = MentoringSlot.id
    WHERE MentoringSlot.cancelled = false
        {$filter->getSqlFromClause('MentoringSlot.startTime', $parameters)}
        {$filter->getSqlToClause('MentoringSlot.startTime', $parameters)}
        
    UNION
    SELECT
        null mentoringSlotId,
        null mentoringSlotCapacity,
        null bookedSlotCount,
        null bookedParticipants,
        NegotiatedMentoring.id negotiatedMentoringId,
        null declaredMentoringId,
        null activityId,
        --
        MentoringRequest.startTime,
        MentoringRequest.endTime,
        MentoringRequest.mediaType,
        MentoringRequest.location,
        --
        null consultantInviteeId,
        null coordinatorInviteeId,
        null activityName,
        null activityDescription,
        null activityNote,
        null invitationCount,
        null inviteeList,
        --
        ConsultationSetup.name consultationSetupName,
        MentoringRequest.Consultant_id consultantId,
        null coordinatorId,
        MentoringRequest.Participant_id participantId,
        Consultant.Program_id programId
        --
    FROM NegotiatedMentoring
        INNER JOIN MentoringRequest ON MentoringRequest.id = NegotiatedMentoring.MentoringRequest_id
        INNER JOIN Consultant ON Consultant.id = MentoringRequest.Consultant_id AND Consultant.active = true and Consultant.Personnel_id = :personnelId
        INNER JOIN ConsultationSetup ON ConsultationSetup.id = MentoringRequest.ConsultationSetup_id
    WHERE 1
        {$filter->getSqlFromClause('MentoringRequest.startTime', $parameters)}
        {$filter->getSqlToClause('MentoringRequest.startTime', $parameters)}

    UNION
    SELECT
        null mentoringSlotId,
        null mentoringSlotCapacity,
        null bookedSlotCount,
        null bookedParticipants,
        null negotiatedMentoringId,
        DeclaredMentoring.id declaredMentoringId,
        null activityId,
        -- 
        DeclaredMentoring.startTime,
        DeclaredMentoring.endTime,
        DeclaredMentoring.mediaType,
        DeclaredMentoring.location,
        -- 
        null consultantInviteeId,
        null coordinatorInviteeId,
        null activityName,
        null activityDescription,
        null activityNote,
        null invitationCount,
        null inviteeList,
        -- 
        ConsultationSetup.name consultationSetupName,
        DeclaredMentoring.Consultant_id consultantId,
        null coordinatorId,
        DeclaredMentoring.Participant_id participantId,
        Consultant.Program_id programId
        -- 
    FROM DeclaredMentoring
        INNER JOIN ConsultationSetup ON ConsultationSetup.id = DeclaredMentoring.ConsultationSetup_id
        INNER JOIN Consultant ON Consultant.id = DeclaredMentoring.Consultant_id AND Consultant.active = true AND Consultant.Personnel_id = :personnelId
        INNER JOIN Participant ON Participant.id = DeclaredMentoring.Participant_id
    WHERE DeclaredMentoring.declaredStatus IN ({$activeDeclaredStatus})
        {$filter->getSqlFromClause('DeclaredMentoring.startTime', $parameters)}
        {$filter->getSqlToClause('DeclaredMentoring.startTime', $parameters)}
    --
    UNION
    SELECT 
        null mentoringSlotId,
        null mentoringSlotCapacity,
        null bookedSlotCount,
        null bookedParticipants,
        null negotiatedMentoringId,
        null declaredMentoringId,
        Activity.id activityId,
        --
        Activity.startDateTime startTime,
        Activity.endDateTime endTime,
        null mediaType,
        Activity.location,
        --
        ConsultantInvitee.id consultantInviteeId,
        null coordinatorInviteeId,
        Activity.name activityName,
        Activity.description activityDescription,
        Activity.note activityNote,
        _a4.invitationCount,
        _a4.inviteeList,
        --
        null consultationSetupName,
        Consultant.id consultantId,
        null coordinatorId,
        null participantId,
        Consultant.Program_id programId
        --
    FROM ConsultantInvitee
        INNER JOIN Consultant ON Consultant.id = ConsultantInvitee.Consultant_id AND Consultant.active = true AND Consultant.Personnel_id = :personnelId
        INNER JOIN Invitee ON Invitee.id = ConsultantInvitee.Invitee_id AND Invitee.cancelled = false
        INNER JOIN Activity ON Activity.id = Invitee.Activity_id AND Activity.cancelled = false
        INNER JOIN ActivityType ON ActivityType.id = Activity.ActivityType_id
        LEFT JOIN (
            SELECT Invitee.Activity_id activityId,
                COUNT(Invitee.id) invitationCount,
                GROUP_CONCAT(
                    COALESCE(
                        CONCAT(User.firstName, ' ', COALESCE(User.lastName, ''), ' [user-participant]', CASE WHEN Invitee.anInitiator THEN ' [initiator]' ELSE '' END), 
                        CONCAT(Client.firstName, ' ', COALESCE(Client.lastName, ''), ' [client-participant]', CASE WHEN Invitee.anInitiator THEN ' [initiator]' ELSE '' END), 
                        CONCAT(Team.name, ' [team-participant]', CASE WHEN Invitee.anInitiator THEN ' [initiator]' ELSE '' END),
                        CONCAT(Coordinator_Personnel.firstName, ' ', COALESCE(Coordinator_Personnel.lastName, ''), ' [coordinator]', CASE WHEN Invitee.anInitiator THEN ' [initiator]' ELSE '' END), 
                        CONCAT(Consultant_Personnel.firstName, ' ', COALESCE(Consultant_Personnel.lastName, ''), ' [consultant]', CASE WHEN Invitee.anInitiator THEN ' [initiator]' ELSE '' END), 
                        CONCAT(Consultant_Personnel.firstName, ' ', COALESCE(Consultant_Personnel.lastName, ''), ' [consultant]', CASE WHEN Invitee.anInitiator THEN ' [initiator]' ELSE '' END), 
                        CONCAT(Manager.name, ' [manager]')
                    )
                    SEPARATOR ', '
                ) inviteeList
            FROM Invitee
                LEFT JOIN CoordinatorInvitee ON CoordinatorInvitee.Invitee_id = Invitee.id
                LEFT JOIN Coordinator ON Coordinator.id = CoordinatorInvitee.Coordinator_id
                LEFT JOIN Personnel AS Coordinator_Personnel ON Coordinator_Personnel.id = Coordinator.Personnel_id

                LEFT JOIN ConsultantInvitee ON ConsultantInvitee.Invitee_id = Invitee.id
                LEFT JOIN Consultant ON Consultant.id = ConsultantInvitee.Consultant_id
                LEFT JOIN Personnel AS Consultant_Personnel ON Consultant_Personnel.id = Consultant.Personnel_id

                LEFT JOIN ManagerInvitee ON ManagerInvitee.Invitee_id = Invitee.id
                LEFT JOIN Manager ON Manager.id = ManagerInvitee.Manager_id

                LEFT JOIN ParticipantInvitee ON ParticipantInvitee.Invitee_id = Invitee.id
                LEFT JOIN UserParticipant ON UserParticipant.Participant_id = ParticipantInvitee.Participant_id
                LEFT JOIN User ON User.id= UserParticipant.User_id
                LEFT JOIN ClientParticipant ON ClientParticipant.Participant_id = ParticipantInvitee.Participant_id
                LEFT JOIN Client ON Client.id = ClientParticipant.Client_id
                LEFT JOIN TeamParticipant ON TeamParticipant.Participant_id = ParticipantInvitee.Participant_id
                LEFT JOIN Team ON Team.id = TeamParticipant.Team_id

            WHERE Invitee.cancelled = false
            GROUP BY activityId
        )_a4 ON _a4.activityId = Activity.id
    WHERE 1
        {$filter->getSqlFromClause('Activity.startDateTime', $parameters)}
        {$filter->getSqlToClause('Activity.startDateTime', $parameters)}
    --
    UNION
    SELECT 
        null mentoringSlotId,
        null mentoringSlotCapacity,
        null bookedSlotCount,
        null bookedParticipants,
        null negotiatedMentoringId,
        null declaredMentoringId,
        Activity.id activityId,
        --
        Activity.startDateTime startTime,
        Activity.endDateTime endTime,
        null mediaType,
        Activity.location,
        --
        null consultantInviteeId,
        CoordinatorInvitee.id coordinatorInviteeId,
        Activity.name activityName,
        Activity.description activityDescription,
        Activity.note activityNote,
        _a5.invitationCount,
        _a5.inviteeList,
        --
        null consultationSetupName,
        null consultantId,
        Coordinator.id coordinatorId,
        null participantId,
        Coordinator.Program_id programId
        
    FROM CoordinatorInvitee
        INNER JOIN Coordinator ON Coordinator.id = CoordinatorInvitee.Coordinator_id AND Coordinator.active = true AND Coordinator.Personnel_id = :personnelId
        INNER JOIN Invitee ON Invitee.id = CoordinatorInvitee.Invitee_id AND Invitee.cancelled = false
        INNER JOIN Activity ON Activity.id = Invitee.Activity_id AND Activity.cancelled = false
        INNER JOIN ActivityType ON ActivityType.id = Activity.ActivityType_id
        LEFT JOIN (
            SELECT Invitee.Activity_id activityId,
                COUNT(Invitee.id) invitationCount,
                GROUP_CONCAT(
                    COALESCE(
                        CONCAT(User.firstName, ' ', COALESCE(User.lastName, ''), ' [user-participant]', CASE WHEN Invitee.anInitiator THEN ' [initiator]' ELSE '' END), 
                        CONCAT(Client.firstName, ' ', COALESCE(Client.lastName, ''), ' [client-participant]', CASE WHEN Invitee.anInitiator THEN ' [initiator]' ELSE '' END), 
                        CONCAT(Team.name, ' [team-participant]', CASE WHEN Invitee.anInitiator THEN ' [initiator]' ELSE '' END),
                        CONCAT(Coordinator_Personnel.firstName, ' ', COALESCE(Coordinator_Personnel.lastName, ''), ' [coordinator]', CASE WHEN Invitee.anInitiator THEN ' [initiator]' ELSE '' END), 
                        CONCAT(Consultant_Personnel.firstName, ' ', COALESCE(Consultant_Personnel.lastName, ''), ' [consultant]', CASE WHEN Invitee.anInitiator THEN ' [initiator]' ELSE '' END), 
                        CONCAT(Consultant_Personnel.firstName, ' ', COALESCE(Consultant_Personnel.lastName, ''), ' [consultant]', CASE WHEN Invitee.anInitiator THEN ' [initiator]' ELSE '' END), 
                        CONCAT(Manager.name, ' [manager]')
                    )
                    SEPARATOR ', '
                ) inviteeList
            FROM Invitee
                LEFT JOIN CoordinatorInvitee ON CoordinatorInvitee.Invitee_id = Invitee.id
                LEFT JOIN Coordinator ON Coordinator.id = CoordinatorInvitee.Coordinator_id
                LEFT JOIN Personnel AS Coordinator_Personnel ON Coordinator_Personnel.id = Coordinator.Personnel_id

                LEFT JOIN ConsultantInvitee ON ConsultantInvitee.Invitee_id = Invitee.id
                LEFT JOIN Consultant ON Consultant.id = ConsultantInvitee.Consultant_id
                LEFT JOIN Personnel AS Consultant_Personnel ON Consultant_Personnel.id = Consultant.Personnel_id

                LEFT JOIN ManagerInvitee ON ManagerInvitee.Invitee_id = Invitee.id
                LEFT JOIN Manager ON Manager.id = ManagerInvitee.Manager_id

                LEFT JOIN ParticipantInvitee ON ParticipantInvitee.Invitee_id = Invitee.id
                LEFT JOIN UserParticipant ON UserParticipant.Participant_id = ParticipantInvitee.Participant_id
                LEFT JOIN User ON User.id= UserParticipant.User_id
                LEFT JOIN ClientParticipant ON ClientParticipant.Participant_id = ParticipantInvitee.Participant_id
                LEFT JOIN Client ON Client.id = ClientParticipant.Client_id
                LEFT JOIN TeamParticipant ON TeamParticipant.Participant_id = ParticipantInvitee.Participant_id
                LEFT JOIN Team ON Team.id = TeamParticipant.Team_id

            WHERE Invitee.cancelled = false
            GROUP BY activityId
        )_a5 ON _a5.activityId = Activity.id
    WHERE 1
        {$filter->getSqlFromClause('Activity.startDateTime', $parameters)}
        {$filter->getSqlToClause('Activity.startDateTime', $parameters)}
)_a
    INNER JOIN Program ON Program.id = _a.programId
    -- 
    LEFT JOIN UserParticipant ON UserParticipant.Participant_id = _a.participantId
    LEFT JOIN User ON User.id= UserParticipant.User_id
    LEFT JOIN ClientParticipant ON ClientParticipant.Participant_id = _a.participantId
    LEFT JOIN Client ON Client.id = ClientParticipant.Client_id
    LEFT JOIN TeamParticipant ON TeamParticipant.Participant_id = _a.participantId
    LEFT JOIN Team ON Team.id = TeamParticipant.Team_id
ORDER BY startTime {$filter->getOrderDirection()}
_STATEMENT;
        $query = $this->em->getConnection()->prepare($statement);
        return $query->executeQuery($parameters)->fetchAllAssociative();
    }


}
