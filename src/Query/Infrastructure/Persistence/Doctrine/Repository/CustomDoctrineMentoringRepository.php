<?php

namespace Query\Infrastructure\Persistence\Doctrine\Repository;

use Doctrine\ORM\EntityManager;
use Query\Domain\Task\Dependency\MentoringFilter;
use Query\Domain\Task\Dependency\MentoringRepository;

class CustomDoctrineMentoringRepository implements MentoringRepository
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

    public function allMentoringsBelongsToPersonnel(
            string $personnelId, int $page, int $pageSize, MentoringFilter $filter)
    {
        $offset = $pageSize * ($page - 1);

        $parameters = [
            "personnelId" => $personnelId,
        ];
        
        $statement = <<<_STATEMENT
SELECT 
    _a.startTime,
    _a.endTime,
    _a.mentorId,
    _a.programId,
    _a.programName,
    _a.consultationSetupId,
    _a.consultationSetupName,
    _a.mentoringSlotId,
    _a.mentoringSlotCapacity,
    _a.bookedMentoringCount,
    _a.mentoringSlotSubmittedReportCount,
    _a.mentoringSlotCancelledStatus,
    _a.mentoringRequestId,
    _a.negotiatedMentoringId,
    _a.mentorReportId,
    _a.mentoringRequestStatus,
    _a.participantId,
    COALESCE(_b.userName, _c.clientName, _d.teamName) participantName
FROM (
    SELECT 
        _u1a.startTime,
        _u1a.endTime,
        _u1a.mentorId,
        _u1a.programId,
        _u1a.programName,
        _u1a.consultationSetupId,
        _u1a.consultationSetupName, 
        _u1a.mentoringSlotId,
        _u1a.capacity mentoringSlotCapacity,
        _u1b.bookedSlotCount bookedMentoringCount,
        _u1b.submittedReportCount mentoringSlotSubmittedReportCount,
        _u1a.cancelled mentoringSlotCancelledStatus,
        null as mentoringRequestId,
        null as negotiatedMentoringId,
        null as mentorReportId,
        null as mentoringRequestStatus,
        null as participantId
   FROM (
        SELECT
            MentoringSlot.id mentoringSlotId,
            MentoringSlot.capacity,
            MentoringSlot.cancelled,
            MentoringSlot.startTime,
            MentoringSlot.endTime,
            Consultant.id mentorId,
            Program.id programId,
            Program.name programName,
            ConsultationSetup.id consultationSetupId,
            ConsultationSetup.name consultationSetupName
        FROM MentoringSlot
        LEFT JOIN Consultant on Consultant.id = MentoringSlot.Mentor_id
        LEFT JOIN Program on Program.id = Consultant.Program_id
        LEFT JOIN ConsultationSetup on ConsultationSetup.id = MentoringSlot.ConsultationSetup_id
        WHERE Consultant.Personnel_id = :personnelId
            AND Consultant.active = true
            {$filter->getSqlFromClause('MentoringSlot', $parameters)}
            {$filter->getSqlToClause('MentoringSlot', $parameters)}
            {$filter->getMentoringSlotFilter()->getSqlCancelldStatusClause('MentoringSlot', $parameters)}
    )_u1a
    LEFT JOIN (
        SELECT
            BookedMentoringSlot.MentoringSlot_id mentoringSlotId,
            COUNT(BookedMentoringSlot.id) bookedSlotCount,
            COUNT(MentorReport.id) submittedReportCount
        FROM BookedMentoringSlot
        LEFT JOIN MentorReport ON MentorReport.Mentoring_id = BookedMentoringSlot.Mentoring_id
        WHERE BookedMentoringSlot.cancelled = false
        GROUP BY mentoringSlotId
    )_u1b ON _u1b.mentoringSlotId = _u1a.mentoringSlotId
    WHERE 1
        {$filter->getMentoringSlotFilter()->getSqlBookingAvailableStatusClause('_u1b.bookedSlotCount', '_u1a.capacity')}
        {$filter->getMentoringSlotFilter()->getSqlMentorReportCompletedStatusClause('_u1b.submittedReportCount', '_u1b.bookedSlotCount')}
    UNION
    SELECT 
        _u2a.startTime,
        _u2a.endTime,
        _u2a.mentorId,
        _u2a.programId,
        _u2a.programName,
        _u2a.consultationSetupId,
        _u2a.consultationSetupName,
        null as mentoringSlotId,
        null as mentoringSlotCapacity,
        null as bookedMentoringCount,
        null as mentoringSlotSubmittedReportCount,
        null as mentoringSlotCancelledStatus,
        _u2a.mentoringRequestId,
        _u2b.id negotiatedMentoringId,
        _u2c.id mentorReportId,
        _u2a.requestStatus mentoringRequestStatus,
        _u2a.participantId
    FROM (
        SELECT 
            MentoringRequest.id mentoringRequestId,
            MentoringRequest.startTime,
            MentoringRequest.endTime,
            CASE MentoringRequest.requestStatus
                WHEN 0 THEN 'requested by participant'
                WHEN 1 THEN 'offered by mentor'
                WHEN 2 THEN 'cancelled by participant'
                WHEN 3 THEN 'rejected by mentor'
                WHEN 4 THEN 'approved by mentor'
                WHEN 5 THEN 'accepted by participant'
            END as requestStatus,
            MentoringRequest.Participant_id participantId,
            Consultant.id mentorId,
            Program.id programId,
            Program.name programName,
            ConsultationSetup.id consultationSetupId,
            ConsultationSetup.name consultationSetupName
        FROM MentoringRequest
        LEFT JOIN Consultant ON Consultant.id = MentoringRequest.Consultant_id
        LEFT JOIN Program ON Program.id = Consultant.Program_id
        LEFT JOIN Participant ON Participant.id = MentoringRequest.Participant_id
        LEFT JOIN ConsultationSetup ON ConsultationSetup.id = MentoringRequest.ConsultationSetup_id
        WHERE Consultant.Personnel_id = :personnelId
            AND Consultant.active = true
            {$filter->getSqlFromClause('MentoringRequest', $parameters)}
            {$filter->getSqlToClause('MentoringRequest', $parameters)}
            {$filter->getMentoringRequestFilter()->getSqlRequestStatusClause('MentoringRequest', $parameters)}
    )_u2a
    LEFT JOIN (
        SELECT id, Mentoring_id, MentoringRequest_id
        FROM NegotiatedMentoring
    )_u2b ON _u2b.MentoringRequest_id = _u2a.mentoringRequestId
    LEFT JOIN (
        SELECT id, Mentoring_id
        FROM MentorReport
    )_u2c ON _u2c.Mentoring_id = _u2b.Mentoring_id
    WHERE 1
        {$filter->getMentoringRequestFilter()->getSqlReportCompletedStatusClause('_u2c.id')}
)_a
LEFT JOIN (
    SELECT CONCAT(User.firstName, ' ', COALESCE(User.lastName, '')) userName, UserParticipant.Participant_id participantId
    FROM UserParticipant
        LEFT JOIN User ON User.id = UserParticipant.User_id
)_b ON _b.participantId = _a.participantId
LEFT JOIN (
    SELECT CONCAT(Client.firstName, ' ', COALESCE(Client.lastName, '')) clientName, ClientParticipant.Participant_id participantId
    FROM ClientParticipant
        LEFT JOIN Client ON Client.id = ClientParticipant.Client_id
)_c ON _c.participantId = _a.participantId
LEFT JOIN (
    SELECT Team.name teamName, TeamParticipant.Participant_id participantId
    FROM TeamParticipant
        LEFT JOIN Team ON Team.id = TeamParticipant.Team_id
)_d ON _d.participantId = _a.participantId
ORDER BY startTime {$filter->getOrderDirection()}
LIMIT {$offset}, {$pageSize}
_STATEMENT;
        $query = $this->em->getConnection()->prepare($statement);
        return [
            'total' => $this->totalCountOfAllMentoringsBelongsToPersonnel($personnelId, $filter),
            'list' => $query->executeQuery($parameters)->fetchAllAssociative(),
        ];
    }

    protected function totalCountOfAllMentoringsBelongsToPersonnel(string $personnelId, MentoringFilter $filter)
    {
        $parameters = [
            "personnelId" => $personnelId,
        ];
        $statement = <<<_STATEMENT
SELECT COUNT(*) total
FROM (
    SELECT _u1a.mentoringSlotId, null mentoringRequestId
    FROM (
        SELECT MentoringSlot.id mentoringSlotId, MentoringSlot.capacity
        FROM MentoringSlot
        LEFT JOIN Consultant on Consultant.id = MentoringSlot.Mentor_id
        WHERE Consultant.Personnel_id = :personnelId
            AND Consultant.active = true
            {$filter->getSqlFromClause('MentoringSlot', $parameters)}
            {$filter->getSqlToClause('MentoringSlot', $parameters)}
            {$filter->getMentoringSlotFilter()->getSqlCancelldStatusClause('MentoringSlot', $parameters)}
    )_u1a
    LEFT JOIN (
        SELECT
            BookedMentoringSlot.MentoringSlot_id mentoringSlotId,
            COUNT(BookedMentoringSlot.id) bookedSlotCount,
            COUNT(MentorReport.id) submittedReportCount
        FROM BookedMentoringSlot
        LEFT JOIN MentorReport ON MentorReport.Mentoring_id = BookedMentoringSlot.Mentoring_id
        WHERE BookedMentoringSlot.cancelled = false
        GROUP BY mentoringSlotId
    )_u1b ON _u1b.mentoringSlotId = _u1a.mentoringSlotId
    WHERE 1
        {$filter->getMentoringSlotFilter()->getSqlBookingAvailableStatusClause('_u1b.bookedSlotCount', '_u1a.capacity')}
        {$filter->getMentoringSlotFilter()->getSqlMentorReportCompletedStatusClause('_u1b.submittedReportCount', '_u1b.bookedSlotCount')}
    UNION
    SELECT null mentoringSlotId, _u2a.mentoringRequestId
    FROM (
        SELECT MentoringRequest.id mentoringRequestId
        FROM MentoringRequest
        LEFT JOIN Consultant ON Consultant.id = MentoringRequest.Consultant_id
        WHERE Consultant.Personnel_id = :personnelId
            AND Consultant.active = true
            {$filter->getSqlFromClause('MentoringRequest', $parameters)}
            {$filter->getSqlToClause('MentoringRequest', $parameters)}
            {$filter->getMentoringRequestFilter()->getSqlRequestStatusClause('MentoringRequest', $parameters)}
    )_u2a
    LEFT JOIN (
        SELECT id, Mentoring_id, MentoringRequest_id
        FROM NegotiatedMentoring
    )_u2b ON _u2b.MentoringRequest_id = _u2a.mentoringRequestId
    LEFT JOIN (
        SELECT id, Mentoring_id
        FROM MentorReport
    )_u2c ON _u2c.Mentoring_id = _u2b.Mentoring_id
    WHERE 1
        {$filter->getMentoringRequestFilter()->getSqlReportCompletedStatusClause('_u2c.id')}
)_a
_STATEMENT;
        $query = $this->em->getConnection()->prepare($statement);
        return $query->executeQuery($parameters)->fetchFirstColumn()[0];
    }

    public function allMentoringsBelongsToParticipant(
            string $participantId, int $page, int $pageSize, MentoringFilter $filter)
    {
        $offset = $pageSize * ($page - 1);
        
        $parameters = [
            "participantId" => $participantId,
        ];
        $statement = <<<_STATEMENT
SELECT 
    _a.startTime,
    _a.endTime,
    _a.mentorId,
    CONCAT(Personnel.firstName, ' ', COALESCE(Personnel.lastName, '')) mentorName,
    _a.consultationSetupId,
    ConsultationSetup.name consultationSetupName,
    _a.bookedMentoringId,
    _a.bookedMentoringCancelledStatus,
    _a.mentoringRequestId,
    _a.negotiatedMentoringId,
    _a.mentoringRequestStatus,
    _a.participantReportId
FROM (
    SELECT 
        MentoringSlot.startTime,
        MentoringSlot.endTime,
        MentoringSlot.Mentor_id mentorId,
        MentoringSlot.ConsultationSetup_id consultationSetupId,
        BookedMentoringSlot.id bookedMentoringId,
        BookedMentoringSlot.cancelled bookedMentoringCancelledStatus,
        null as mentoringRequestId,
        null as negotiatedMentoringId,
        null as mentoringRequestStatus,
        ParticipantReport.id participantReportId
    FROM BookedMentoringSlot
    LEFT JOIN MentoringSlot ON MentoringSlot.id = BookedMentoringSlot.MentoringSlot_id
    LEFT JOIN ParticipantReport ON ParticipantReport.Mentoring_id = BookedMentoringSlot.Mentoring_id
    WHERE BookedMentoringSlot.Participant_id = :participantId
        {$filter->getSqlFromClause('MentoringSlot', $parameters)}
        {$filter->getSqlToClause('MentoringSlot', $parameters)}
        {$filter->getMentoringSlotFilter()->getSqlCancelldStatusClause('BookedMentoringSlot', $parameters)}
        {$filter->getMentoringSlotFilter()->getSqlParticipantReportCompletedStatusClause('ParticipantReport.id')}
        
    UNION
        
    SELECT 
        MentoringRequest.startTime,
        MentoringRequest.endTime,
        MentoringRequest.Consultant_id mentorId,
        MentoringRequest.ConsultationSetup_id consultationSetupId,
        null as bookedMentoringId,
        null as bookedMentoringCancelledStatus,
        MentoringRequest.id mentoringRequestId,
        NegotiatedMentoring.id negotiatedMentoringId,
        CASE MentoringRequest.requestStatus
            WHEN 0 THEN 'requested by participant'
            WHEN 1 THEN 'offered by mentor'
            WHEN 2 THEN 'cancelled by participant'
            WHEN 3 THEN 'rejected by mentor'
            WHEN 4 THEN 'approved by mentor'
            WHEN 5 THEN 'accepted by participant'
        END as mentoringRequestStatus,
        ParticipantReport.id participantReportId
    FROM MentoringRequest
    LEFT JOIN NegotiatedMentoring ON NegotiatedMentoring.MentoringRequest_id = MentoringRequest.id
    LEFT JOIN ParticipantReport ON ParticipantReport.Mentoring_id = NegotiatedMentoring.Mentoring_id
    WHERE MentoringRequest.Participant_id = :participantId
        {$filter->getSqlFromClause('MentoringRequest', $parameters)}
        {$filter->getSqlToClause('MentoringRequest', $parameters)}
        {$filter->getMentoringRequestFilter()->getSqlRequestStatusClause('MentoringRequest', $parameters)}
        {$filter->getMentoringRequestFilter()->getSqlReportCompletedStatusClause('ParticipantReport.id')}
)_a
LEFT JOIN Consultant ON Consultant.id = _a.mentorId
LEFT JOIN Personnel ON Personnel.id = Consultant.Personnel_id
LEFT JOIN ConsultationSetup ON ConsultationSetup.id = _a.consultationSetupId
ORDER BY startTime {$filter->getOrderDirection()}
LIMIT {$offset}, {$pageSize}
_STATEMENT;
        $query = $this->em->getConnection()->prepare($statement);
        return [
            'total' => $this->totalCountOfAllMentoringsBelongsToParticipant($participantId, $filter),
            'list' => $query->executeQuery($parameters)->fetchAllAssociative(),
        ];
    }

    public function totalCountOfAllMentoringsBelongsToParticipant(string $participantId, MentoringFilter $filter)
    {
        $parameters = [
            "participantId" => $participantId,
        ];
        
        $statement = <<<_STATEMENT
SELECT COUNT(*) total
FROM (
    SELECT BookedMentoringSlot.id bookedMentoringId, null as mentoringRequestId
    FROM BookedMentoringSlot
    LEFT JOIN MentoringSlot ON MentoringSlot.id = BookedMentoringSlot.MentoringSlot_id
    LEFT JOIN ParticipantReport ON ParticipantReport.Mentoring_id = BookedMentoringSlot.Mentoring_id
    WHERE BookedMentoringSlot.Participant_id = :participantId
        {$filter->getSqlFromClause('MentoringSlot', $parameters)}
        {$filter->getSqlToClause('MentoringSlot', $parameters)}
        {$filter->getMentoringSlotFilter()->getSqlCancelldStatusClause('BookedMentoringSlot', $parameters)}
        {$filter->getMentoringSlotFilter()->getSqlParticipantReportCompletedStatusClause('ParticipantReport.id')}
        
    UNION
        
    SELECT null as bookedMentoringId, MentoringRequest.id mentoringRequestId
    FROM MentoringRequest
    LEFT JOIN NegotiatedMentoring ON NegotiatedMentoring.MentoringRequest_id = MentoringRequest.id
    LEFT JOIN ParticipantReport ON ParticipantReport.Mentoring_id = NegotiatedMentoring.Mentoring_id
    WHERE MentoringRequest.Participant_id = :participantId
        {$filter->getSqlFromClause('MentoringRequest', $parameters)}
        {$filter->getSqlToClause('MentoringRequest', $parameters)}
        {$filter->getMentoringRequestFilter()->getSqlRequestStatusClause('MentoringRequest', $parameters)}
        {$filter->getMentoringRequestFilter()->getSqlReportCompletedStatusClause('ParticipantReport.id')}
)_a
_STATEMENT;
        $query = $this->em->getConnection()->prepare($statement);
        return $query->executeQuery($parameters)->fetchFirstColumn()[0];
    }

}
