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
    Program.id programId,
    Program.name programName,
    ConsultationSetup.id consultationSetupId,
    ConsultationSetup.name consultationSetupName,
    _a.mentoringSlotId,
    _a.mentoringSlotCapacity,
    _a.bookedMentoringCount,
    _a.mentoringSlotSubmittedReportCount,
    _a.mentoringSlotCancelledStatus,
    _a.mentoringRequestId,
    _a.negotiatedMentoringId,
    _a.mentorReportId,
    _a.mentoringRequestStatus,
    _a.declaredMentoringId,
    _a.declaredMentoringStatus,
    _a.participantId,
    COALESCE(
        CONCAT(User.firstName, ' ', COALESCE(User.lastName, '')), 
        CONCAT(Client.firstName, ' ', COALESCE(Client.lastName, '')), 
        Team.name
    ) participantName
FROM (
    SELECT 
        _u1a.startTime,
        _u1a.endTime,
        _u1a.mentorId,
        _u1a.programId,
        _u1a.consultationSetupId,
        _u1a.mentoringSlotId,
        _u1a.capacity mentoringSlotCapacity,
        _u1b.bookedSlotCount bookedMentoringCount,
        _u1b.submittedReportCount mentoringSlotSubmittedReportCount,
        _u1a.cancelled mentoringSlotCancelledStatus,
        null as mentoringRequestId,
        null as negotiatedMentoringId,
        null as mentorReportId,
        null as mentoringRequestStatus,
        null as declaredMentoringId,
        null as declaredMentoringStatus,
        null as participantId
   FROM (
        SELECT
            MentoringSlot.id mentoringSlotId,
            MentoringSlot.capacity,
            MentoringSlot.cancelled,
            MentoringSlot.startTime,
            MentoringSlot.endTime,
            Consultant.id mentorId,
            Consultant.Program_id programId,
            MentoringSlot.ConsultationSetup_id consultationSetupId
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
        
    SELECT 
        MentoringRequest.startTime,
        MentoringRequest.endTime,
        Consultant.id mentorId,
        Consultant.Program_id programId,
        MentoringRequest.ConsultationSetup_Id consultationSetupId,
        null as mentoringSlotId,
        null as mentoringSlotCapacity,
        null as bookedMentoringCount,
        null as mentoringSlotSubmittedReportCount,
        null as mentoringSlotCancelledStatus,
        MentoringRequest.id as mentoringRequestId,
        NegotiatedMentoring.id as negotiatedMentoringId,
        MentorReport.id mentorReportId,
        CASE MentoringRequest.requestStatus
                WHEN 0 THEN 'requested by participant'
                WHEN 1 THEN 'offered by mentor'
                WHEN 2 THEN 'cancelled by participant'
                WHEN 3 THEN 'rejected by mentor'
                WHEN 4 THEN 'approved by mentor'
                WHEN 5 THEN 'accepted by participant'
        END as requestStatus,
        null declaredMentoringId,
        null declaredMentoringStatus,
        MentoringRequest.Participant_id participantId
    FROM MentoringRequest
    LEFT JOIN NegotiatedMentoring ON NegotiatedMentoring.MentoringRequest_id = MentoringRequest.id
    LEFT JOIN MentorReport ON MentorReport.Mentoring_id = NegotiatedMentoring.Mentoring_id
    LEFT JOIN Consultant ON Consultant.id = MentoringRequest.Consultant_id
    WHERE Consultant.Personnel_id = :personnelId
        AND Consultant.active = true
        {$filter->getSqlFromClause('MentoringRequest', $parameters)}
        {$filter->getSqlToClause('MentoringRequest', $parameters)}
        {$filter->getMentoringRequestFilter()->getSqlRequestStatusClause('MentoringRequest', $parameters)}
        {$filter->getMentoringRequestFilter()->getSqlReportCompletedStatusClause('MentorReport.id')}
        
    UNION
    
    SELECT 
        DeclaredMentoring.startTime,
        DeclaredMentoring.endTime,
        Consultant.id mentorId,
        Consultant.Program_id programId,
        DeclaredMentoring.ConsultationSetup_Id consultationSetupId,
        null as mentoringSlotId,
        null as mentoringSlotCapacity,
        null as bookedMentoringCount,
        null as mentoringSlotSubmittedReportCount,
        null as mentoringSlotCancelledStatus,
        null as mentoringRequestId,
        null as negotiatedMentoringId,
        MentorReport.id mentorReportId,
        null asmentoringRequestStatus,
        DeclaredMentoring.id declaredMentoringId,
        CASE DeclaredMentoring.declaredStatus
            WHEN 1 THEN 'declared by mentor'
            WHEN 2 THEN 'declared by participant'
            WHEN 3 THEN 'cancelled'
            WHEN 4 THEN 'approved by mentor'
            WHEN 5 THEN 'denied by mentor'
            WHEN 6 THEN 'approved by participant'
            WHEN 7 THEN 'denied by participant'
        END as declaredMentoringStatus,
        DeclaredMentoring.Participant_id participantId
    FROM DeclaredMentoring
    LEFT JOIN Consultant ON Consultant.id = DeclaredMentoring.Consultant_id
    LEFT JOIN MentorReport ON MentorReport.Mentoring_id = DeclaredMentoring.Mentoring_id
    WHERE Consultant.Personnel_id = :personnelId
        AND Consultant.active = true
        {$filter->getSqlFromClause('DeclaredMentoring', $parameters)}
        {$filter->getSqlToClause('DeclaredMentoring', $parameters)}
        {$filter->getDeclaredMentoringFilter()->getSqlRequestStatusClause('DeclaredMentoring', $parameters)}
        {$filter->getDeclaredMentoringFilter()->getSqlReportCompletedStatusClause('MentorReport.id')}
)_a
LEFT JOIN UserParticipant ON UserParticipant.Participant_id = _a.participantId
LEFT JOIN User ON User.id= UserParticipant.User_id
LEFT JOIN ClientParticipant ON ClientParticipant.Participant_id = _a.participantId
LEFT JOIN Client ON Client.id = ClientParticipant.Client_id
LEFT JOIN TeamParticipant ON TeamParticipant.Participant_id = _a.participantId
LEFT JOIN Team ON Team.id = TeamParticipant.Team_id
LEFT JOIN Program ON Program.id = _a.programId
LEFT JOIN ConsultationSetup ON ConsultationSetup.id = _a.consultationSetupId
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
    SELECT _u1a.mentoringSlotId, null mentoringRequestId, null declaredMentoringId
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
        
    SELECT null mentoringSlotId, MentoringRequest.id mentoringRequestId, null declaredMentoringId
    FROM MentoringRequest
    LEFT JOIN Consultant ON Consultant.id = MentoringRequest.Consultant_id
    LEFT JOIN NegotiatedMentoring ON NegotiatedMentoring.MentoringRequest_id = MentoringRequest.id
    LEFT JOIN MentorReport ON MentorReport.Mentoring_id = NegotiatedMentoring.Mentoring_id
    WHERE Consultant.Personnel_id = :personnelId
        AND Consultant.active = true
        {$filter->getSqlFromClause('MentoringRequest', $parameters)}
        {$filter->getSqlToClause('MentoringRequest', $parameters)}
        {$filter->getMentoringRequestFilter()->getSqlRequestStatusClause('MentoringRequest', $parameters)}
        {$filter->getMentoringRequestFilter()->getSqlReportCompletedStatusClause('MentorReport.id')}
    
    UNION
    
    SELECT null mentoringSlotId, null mentoringRequestId, DeclaredMentoring.id declaredMentoringId
    FROM DeclaredMentoring
    LEFT JOIN Consultant ON Consultant.id = DeclaredMentoring.Consultant_id
    LEFT JOIN MentorReport ON MentorReport.Mentoring_id = DeclaredMentoring.Mentoring_id
    WHERE Consultant.Personnel_id = :personnelId
        AND Consultant.active = true
        {$filter->getSqlFromClause('DeclaredMentoring', $parameters)}
        {$filter->getSqlToClause('DeclaredMentoring', $parameters)}
        {$filter->getDeclaredMentoringFilter()->getSqlRequestStatusClause('DeclaredMentoring', $parameters)}
        {$filter->getDeclaredMentoringFilter()->getSqlReportCompletedStatusClause('MentorReport.id')}
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
    _a.declaredMentoringId,
    _a.declaredMentoringStatus,
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
        null declaredMentoringId,
        null declaredMentoringStatus,
        ParticipantReport.id participantReportId
    FROM BookedMentoringSlot
    LEFT JOIN Participant ON Participant.id = BookedMentoringSlot.Participant_id
    LEFT JOIN MentoringSlot ON MentoringSlot.id = BookedMentoringSlot.MentoringSlot_id
    LEFT JOIN ParticipantReport ON ParticipantReport.Mentoring_id = BookedMentoringSlot.Mentoring_id
    WHERE Participant.id = :participantId
        AND Participant.active = true
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
        null declaredMentoringId,
        null declaredMentoringStatus,
        ParticipantReport.id participantReportId
    FROM MentoringRequest
    LEFT JOIN Participant ON Participant.id = MentoringRequest.Participant_id
    LEFT JOIN NegotiatedMentoring ON NegotiatedMentoring.MentoringRequest_id = MentoringRequest.id
    LEFT JOIN ParticipantReport ON ParticipantReport.Mentoring_id = NegotiatedMentoring.Mentoring_id
    WHERE Participant.id = :participantId
        AND Participant.active = true
        {$filter->getSqlFromClause('MentoringRequest', $parameters)}
        {$filter->getSqlToClause('MentoringRequest', $parameters)}
        {$filter->getMentoringRequestFilter()->getSqlRequestStatusClause('MentoringRequest', $parameters)}
        {$filter->getMentoringRequestFilter()->getSqlReportCompletedStatusClause('ParticipantReport.id')}
        
    UNION
        
    SELECT 
        DeclaredMentoring.startTime,
        DeclaredMentoring.endTime,
        DeclaredMentoring.Consultant_id mentorId,
        DeclaredMentoring.ConsultationSetup_id consultationSetupId,
        null as bookedMentoringId,
        null as bookedMentoringCancelledStatus,
        null mentoringRequestId,
        null negotiatedMentoringId,
        null mentoringRequestStatus,
        DeclaredMentoring.id declaredMentoringId,
        CASE DeclaredMentoring.declaredStatus
            WHEN 1 THEN 'declared by mentor'
            WHEN 2 THEN 'declared by participant'
            WHEN 3 THEN 'cancelled'
            WHEN 4 THEN 'approved by mentor'
            WHEN 5 THEN 'denied by mentor'
            WHEN 6 THEN 'approved by participant'
            WHEN 7 THEN 'denied by participant'
        END as declaredMentoringStatus,
        ParticipantReport.id participantReportId
    FROM DeclaredMentoring
    LEFT JOIN Participant ON Participant.id = DeclaredMentoring.Participant_id
    LEFT JOIN ParticipantReport ON ParticipantReport.Mentoring_id = DeclaredMentoring.Mentoring_id
    WHERE Participant.id = :participantId
        AND Participant.active = true
        {$filter->getSqlFromClause('DeclaredMentoring', $parameters)}
        {$filter->getSqlToClause('DeclaredMentoring', $parameters)}
        {$filter->getDeclaredMentoringFilter()->getSqlRequestStatusClause('DeclaredMentoring', $parameters)}
        {$filter->getDeclaredMentoringFilter()->getSqlReportCompletedStatusClause('ParticipantReport.id')}
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
    SELECT BookedMentoringSlot.id bookedMentoringId, null as mentoringRequestId, null declaredMentoringId
    FROM BookedMentoringSlot
    LEFT JOIN Participant ON Participant.id = BookedMentoringSlot.Participant_id
    LEFT JOIN MentoringSlot ON MentoringSlot.id = BookedMentoringSlot.MentoringSlot_id
    LEFT JOIN ParticipantReport ON ParticipantReport.Mentoring_id = BookedMentoringSlot.Mentoring_id
    WHERE Participant.id = :participantId
        AND Participant.active = true
        {$filter->getSqlFromClause('MentoringSlot', $parameters)}
        {$filter->getSqlToClause('MentoringSlot', $parameters)}
        {$filter->getMentoringSlotFilter()->getSqlCancelldStatusClause('BookedMentoringSlot', $parameters)}
        {$filter->getMentoringSlotFilter()->getSqlParticipantReportCompletedStatusClause('ParticipantReport.id')}
        
    UNION
        
    SELECT null as bookedMentoringId, MentoringRequest.id mentoringRequestId, null declaredMentoringId
    FROM MentoringRequest
    LEFT JOIN Participant ON Participant.id = MentoringRequest.Participant_id
    LEFT JOIN NegotiatedMentoring ON NegotiatedMentoring.MentoringRequest_id = MentoringRequest.id
    LEFT JOIN ParticipantReport ON ParticipantReport.Mentoring_id = NegotiatedMentoring.Mentoring_id
    WHERE Participant.id = :participantId
        AND Participant.active = true
        {$filter->getSqlFromClause('MentoringRequest', $parameters)}
        {$filter->getSqlToClause('MentoringRequest', $parameters)}
        {$filter->getMentoringRequestFilter()->getSqlRequestStatusClause('MentoringRequest', $parameters)}
        {$filter->getMentoringRequestFilter()->getSqlReportCompletedStatusClause('ParticipantReport.id')}
    
    UNION
    
    SELECT null bookedMentoringId, null mentoringRequestId, DeclaredMentoring.id declaredMentoringId
    FROM DeclaredMentoring
    LEFT JOIN Participant ON Participant.id = DeclaredMentoring.Participant_id
    LEFT JOIN ParticipantReport ON ParticipantReport.Mentoring_id = DeclaredMentoring.Mentoring_id
    WHERE Participant.id = :participantId
        AND Participant.active = true
        {$filter->getSqlFromClause('DeclaredMentoring', $parameters)}
        {$filter->getSqlToClause('DeclaredMentoring', $parameters)}
        {$filter->getDeclaredMentoringFilter()->getSqlRequestStatusClause('DeclaredMentoring', $parameters)}
        {$filter->getDeclaredMentoringFilter()->getSqlReportCompletedStatusClause('ParticipantReport.id')}
)_a
_STATEMENT;
        $query = $this->em->getConnection()->prepare($statement);
        return $query->executeQuery($parameters)->fetchFirstColumn()[0];
    }

}
