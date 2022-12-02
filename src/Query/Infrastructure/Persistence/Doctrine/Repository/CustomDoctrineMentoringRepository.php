<?php

namespace Query\Infrastructure\Persistence\Doctrine\Repository;

use Doctrine\ORM\EntityManager;
use Query\Domain\Task\Dependency\ExtendedMentoringFilter;
use Query\Domain\Task\Dependency\MentoringFilter;
use Query\Domain\Task\Dependency\MentoringRepository;
use Query\Domain\Task\Personnel\MentoringListFilterForCoordinator;
use SharedContext\Domain\ValueObject\DeclaredMentoringStatus;
use SharedContext\Domain\ValueObject\MentoringRequestStatus;

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

    public function allValidMentoringsInProgram(string $programId, ExtendedMentoringFilter $filter)
    {
        $offset = $filter->getPageSize() * ($filter->getPage() - 1);
        
        $parameters = [
            "programId" => $programId,
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
        null as mentoringRequestId,
        null as negotiatedMentoringId,
        null as mentoringRequestStatus,
        null declaredMentoringId,
        null declaredMentoringStatus,
        ParticipantReport.id participantReportId
    FROM BookedMentoringSlot
    INNER JOIN Participant ON Participant.id = BookedMentoringSlot.Participant_id
    INNER JOIN MentoringSlot ON MentoringSlot.id = BookedMentoringSlot.MentoringSlot_id
    LEFT JOIN ParticipantReport ON ParticipantReport.Mentoring_id = BookedMentoringSlot.Mentoring_id
    WHERE Participant.Program_id = :programId
        AND Participant.active = true
        AND BookedMentoringSlot.cancelled = false
        {$filter->getSqlParticipantIdClause('Participant', $parameters)}
        {$filter->getSqlFromClause('MentoringSlot', $parameters)}
        {$filter->getSqlToClause('MentoringSlot', $parameters)}
        
    UNION
        
    SELECT 
        MentoringRequest.startTime,
        MentoringRequest.endTime,
        MentoringRequest.Consultant_id mentorId,
        MentoringRequest.ConsultationSetup_id consultationSetupId,
        null as bookedMentoringId,
        MentoringRequest.id mentoringRequestId,
        NegotiatedMentoring.id negotiatedMentoringId,
        CASE MentoringRequest.requestStatus
            WHEN 0 THEN 'requested by participant'
            WHEN 1 THEN 'offered by mentor'
            WHEN 4 THEN 'approved by mentor'
            WHEN 5 THEN 'accepted by participant'
        END as mentoringRequestStatus,
        null declaredMentoringId,
        null declaredMentoringStatus,
        ParticipantReport.id participantReportId
    FROM MentoringRequest
    INNER JOIN Participant ON Participant.id = MentoringRequest.Participant_id
    LEFT JOIN NegotiatedMentoring ON NegotiatedMentoring.MentoringRequest_id = MentoringRequest.id
    LEFT JOIN ParticipantReport ON ParticipantReport.Mentoring_id = NegotiatedMentoring.Mentoring_id
    WHERE Participant.Program_id = :programId
        AND Participant.active = true
        AND MentoringRequest.requestStatus IN (0, 1, 4, 5)
        {$filter->getSqlParticipantIdClause('Participant', $parameters)}
        {$filter->getSqlFromClause('MentoringRequest', $parameters)}
        {$filter->getSqlToClause('MentoringRequest', $parameters)}
        
    UNION
        
    SELECT 
        DeclaredMentoring.startTime,
        DeclaredMentoring.endTime,
        DeclaredMentoring.Consultant_id mentorId,
        DeclaredMentoring.ConsultationSetup_id consultationSetupId,
        null as bookedMentoringId,
        null mentoringRequestId,
        null negotiatedMentoringId,
        null mentoringRequestStatus,
        DeclaredMentoring.id declaredMentoringId,
        CASE DeclaredMentoring.declaredStatus
            WHEN 1 THEN 'declared by mentor'
            WHEN 2 THEN 'declared by participant'
            WHEN 4 THEN 'approved by mentor'
            WHEN 6 THEN 'approved by participant'
        END as declaredMentoringStatus,
        ParticipantReport.id participantReportId
    FROM DeclaredMentoring
    INNER JOIN Participant ON Participant.id = DeclaredMentoring.Participant_id
    LEFT JOIN ParticipantReport ON ParticipantReport.Mentoring_id = DeclaredMentoring.Mentoring_id
    WHERE Participant.Program_id = :programId
        AND Participant.active = true
        AND DeclaredMentoring.declaredStatus IN (1, 2, 4, 6)
        {$filter->getSqlParticipantIdClause('Participant', $parameters)}
        {$filter->getSqlFromClause('DeclaredMentoring', $parameters)}
        {$filter->getSqlToClause('DeclaredMentoring', $parameters)}
)_a
INNER JOIN Consultant ON Consultant.id = _a.mentorId
INNER JOIN Personnel ON Personnel.id = Consultant.Personnel_id
INNER JOIN ConsultationSetup ON ConsultationSetup.id = _a.consultationSetupId
ORDER BY startTime {$filter->getOrderDirection()}
LIMIT {$offset}, {$filter->getPageSize()}
_STATEMENT;
        $query = $this->em->getConnection()->prepare($statement);
        return [
            'total' => $this->totalCountOfAllValidMentoringsInProgram($programId, $filter),
            'list' => $query->executeQuery($parameters)->fetchAllAssociative(),
        ];
    }
    public function totalCountOfAllValidMentoringsInProgram(string $programId, ExtendedMentoringFilter $filter)
    {
        $parameters = [
            "programId" => $programId,
        ];
        
        $statement = <<<_STATEMENT
SELECT COUNT(*) total
FROM (
    SELECT BookedMentoringSlot.id bookedMentoringId, null as mentoringRequestId, null declaredMentoringId
    FROM BookedMentoringSlot
    INNER JOIN Participant ON Participant.id = BookedMentoringSlot.Participant_id
    INNER JOIN MentoringSlot ON MentoringSlot.id = BookedMentoringSlot.MentoringSlot_id
    WHERE Participant.Program_id = :programId
        AND Participant.active = true
        AND BookedMentoringSlot.cancelled = false
        {$filter->getSqlParticipantIdClause('Participant', $parameters)}
        {$filter->getSqlFromClause('MentoringSlot', $parameters)}
        {$filter->getSqlToClause('MentoringSlot', $parameters)}
        
    UNION
        
    SELECT null as bookedMentoringId, MentoringRequest.id mentoringRequestId, null declaredMentoringId
    FROM MentoringRequest
    INNER JOIN Participant ON Participant.id = MentoringRequest.Participant_id
    WHERE Participant.Program_id = :programId
        AND Participant.active = true
        AND MentoringRequest.requestStatus IN (0, 1, 4, 5)
        {$filter->getSqlParticipantIdClause('Participant', $parameters)}
        {$filter->getSqlFromClause('MentoringRequest', $parameters)}
        {$filter->getSqlToClause('MentoringRequest', $parameters)}
    
    UNION
    
    SELECT null bookedMentoringId, null mentoringRequestId, DeclaredMentoring.id declaredMentoringId
    FROM DeclaredMentoring
    INNER JOIN Participant ON Participant.id = DeclaredMentoring.Participant_id
    WHERE Participant.Program_id = :programId
        AND Participant.active = true
        AND DeclaredMentoring.declaredStatus IN (1, 2, 4, 6)
        {$filter->getSqlParticipantIdClause('Participant', $parameters)}
        {$filter->getSqlFromClause('DeclaredMentoring', $parameters)}
        {$filter->getSqlToClause('DeclaredMentoring', $parameters)}
)_a
_STATEMENT;
        $query = $this->em->getConnection()->prepare($statement);
        return $query->executeQuery($parameters)->fetchFirstColumn()[0];
    }

    public function mentoringListInAllProgramCoordinatedByPersonnel(string $personnelId,
            MentoringListFilterForCoordinator $filter)
    {
        $parameters = [
            'personnelId' => $personnelId,
        ];
        $irrelevantDeclaredMentoringStatus = implode(", ", [
            DeclaredMentoringStatus::CANCELLED, 
            DeclaredMentoringStatus::DENIED_BY_MENTOR, 
            DeclaredMentoringStatus::DENIED_BY_PARTICIPANT
        ]);
        
        $irrelevantMentoringRequestStatus = implode(", ", [
            MentoringRequestStatus::CANCELLED, 
            MentoringRequestStatus::REJECTED
        ]);
        
        $sql = <<<_SQL
SELECT
    _mentoring.mentoringRequestId,
            _mentoring.mentoringRequestStatus,
    _mentoring.bookedMentoringSlotId,
    _mentoring.declaredMentoringId,
            _mentoring.declaredMentoringStatus,
        _mentoring.participantId,
        COALESCE(
            CONCAT(User.firstName, ' ', COALESCE(User.lastName, '')), 
            CONCAT(Client.firstName, ' ', COALESCE(Client.lastName, '')), 
            Team.name
        ) participantName,
        _mentoring.reportSubmitted,
    _mentoring.mentoringSlotId,
        _mentoring.capacity,
        _mentoring.totalBooking,
        _mentoring.totalSubmittedReport,
    _mentoring.startTime,
    _mentoring.endTime,
                
    _mentoring.consultantId,
        CONCAT(Personnel.firstName, ' ', COALESCE(Personnel.lastName, '')) consultantName,
                
    Coordinator.id coordinatorId,
        Coordinator.Program_id programId,
        Program.name programName
FROM (
    SELECT
        MentoringRequest.id mentoringRequestId,
                MentoringRequest.requestStatus mentoringRequestStatus,
        null bookedMentoringSlotId,
        null declaredMentoringId,
                null declaredMentoringStatus,
            MentoringRequest.Participant_id participantId,
            IF(MentorReport.id IS NOT NULL AND ParticipantReport.id IS NOT NULL, true, false) reportSubmitted,
        null mentoringSlotId,
            null capacity,
            null totalBooking,
            null totalSubmittedReport,
        MentoringRequest.startTime,
        MentoringRequest.endTime,
        MentoringRequest.Consultant_id consultantId
    FROM MentoringRequest
        LEFT JOIN NegotiatedMentoring ON NegotiatedMentoring.MentoringRequest_id = MentoringRequest.id
        LEFT JOIN Mentoring ON Mentoring.id = NegotiatedMentoring.Mentoring_id
        LEFT JOIN MentorReport ON MentorReport.Mentoring_id = Mentoring.id
        LEFT JOIN ParticipantReport ON ParticipantReport.Mentoring_id = Mentoring.id
    WHERE MentoringRequest.requestStatus NOT IN ({$irrelevantMentoringRequestStatus})
            
    UNION
    SELECT
        null mentoringRequestId,
                null mentoringRequestStatus,
        null bookedMentoringSlotId,
        null declaredMentoringId,
                null declaredMentoringStatus,
            null participantId,
            null reportSubmitted,
        MentoringSlot.id mentoringSlotId,
            MentoringSlot.capacity,
            _bookedMentoringSlot.totalBooking,
            _bookedMentoringSlot.totalSubmittedReport,

        MentoringSlot.startTime,
        MentoringSlot.endTime,
        MentoringSlot.Mentor_id consultantId
    FROM MentoringSlot
        INNER JOIN Consultant ON Consultant.id = MentoringSlot.Mentor_id AND Consultant.active = true
        LEFT JOIN (
            SELECT MentoringSlot_id, COUNT(*) totalBooking, COUNT(MentorReport.id) + COUNT(ParticipantReport.id) totalSubmittedReport
            FROM BookedMentoringSlot
                LEFT JOIN Mentoring ON Mentoring.id = BookedMentoringSlot.Mentoring_id
                LEFT JOIN MentorReport ON MentorReport.Mentoring_id = Mentoring.id
                LEFT JOIN ParticipantReport ON ParticipantReport.Mentoring_id = Mentoring.id
            WHERE BookedMentoringSlot.cancelled = false
            GROUP BY MentoringSlot_id
        )_bookedMentoringSlot ON _bookedMentoringSlot.MentoringSlot_id = MentoringSlot.id
    WHERE MentoringSlot.cancelled = false
                
    UNION
    SELECT
        null mentoringRequestId,
                null mentoringRequestStatus,
        BookedMentoringSlot.id bookedMentoringSlotId,
        null declaredMentoringId,
                null declaredMentoringStatus,
            BookedMentoringSlot.Participant_id participantId,
            IF(MentorReport.id IS NOT NULL AND ParticipantReport.id IS NOT NULL, true, false) reportSubmitted,
        null mentoringSlotId,
            null capacity,
            null totalBooking,
            null totalSubmittedReport,
        MentoringSlot.startTime,
        MentoringSlot.endTime,
        MentoringSlot.Mentor_id consultantId
    FROM BookedMentoringSlot
        INNER JOIN MentoringSlot ON MentoringSlot.id = BookedMentoringSlot.MentoringSlot_id
        LEFT JOIN Mentoring ON Mentoring.id = BookedMentoringSlot.Mentoring_id
        LEFT JOIN MentorReport ON MentorReport.Mentoring_id = Mentoring.id
        LEFT JOIN ParticipantReport ON ParticipantReport.Mentoring_id = Mentoring.id
    WHERE BookedMentoringSlot.cancelled = false
                
    UNION
    SELECT
        null mentoringRequestId,
                null mentoringRequestStatus,
        null bookedMentoringSlotId,
        DeclaredMentoring.id declaredMentoringId,
                DeclaredMentoring.declaredStatus declaredMentoringStatus,
            DeclaredMentoring.Participant_id participantId,
            IF(MentorReport.id IS NOT NULL AND ParticipantReport.id IS NOT NULL, true, false) reportSubmitted,
        null mentoringSlotId,
            null capacity,
            null totalBooking,
            null totalSubmittedReport,
        DeclaredMentoring.startTime,
        DeclaredMentoring.endTime,
        DeclaredMentoring.Consultant_id consultantId
    FROM DeclaredMentoring
        LEFT JOIN Mentoring ON Mentoring.id = DeclaredMentoring.Mentoring_id
        LEFT JOIN MentorReport ON MentorReport.Mentoring_id = Mentoring.id
        LEFT JOIN ParticipantReport ON ParticipantReport.Mentoring_id = Mentoring.id
    WHERE DeclaredMentoring.declaredStatus NOT IN ({$irrelevantDeclaredMentoringStatus})
    
)_mentoring
    INNER JOIN Consultant ON Consultant.id = _mentoring.consultantId
    INNER JOIN Personnel ON Personnel.id = Consultant.Personnel_id
    
    INNER JOIN Coordinator
        ON Coordinator.Program_id = Consultant.Program_id
        AND Coordinator.Personnel_id = :personnelId
        AND Coordinator.active = true
    INNER JOIN Program ON Program.id = Coordinator.Program_id
    
    LEFT JOIN Participant ON Participant.id = _mentoring.participantId
    LEFT JOIN UserParticipant ON UserParticipant.Participant_id = Participant.id
    LEFT JOIN User ON User.id= UserParticipant.User_id
    LEFT JOIN ClientParticipant ON ClientParticipant.Participant_id = Participant.id
    LEFT JOIN Client ON Client.id = ClientParticipant.Client_id
    LEFT JOIN TeamParticipant ON TeamParticipant.Participant_id = Participant.id
    LEFT JOIN Team ON Team.id = TeamParticipant.Team_id
WHERE 1
    {$filter->getCriteriaStatement($parameters)}
{$filter->getOrderStatement()}
{$filter->getLimitStatement()}
_SQL;
    
        $query = $this->em->getConnection()->prepare($sql);
        return [
            'total' => $this->totalMentoringListInAllProgramCoordinatedByPersonnel($personnelId, $filter),
            'list' => $query->executeQuery($parameters)->fetchAllAssociative(),
        ];
        return $query->executeQuery($parameters)->fetchFirstColumn()[0];
    }
    public function totalMentoringListInAllProgramCoordinatedByPersonnel(string $personnelId,
            MentoringListFilterForCoordinator $filter)
    {
        $parameters = [
            'personnelId' => $personnelId,
        ];
        $irrelevantDeclaredMentoringStatus = implode(", ", [
            DeclaredMentoringStatus::CANCELLED, 
            DeclaredMentoringStatus::DENIED_BY_MENTOR, 
            DeclaredMentoringStatus::DENIED_BY_PARTICIPANT
        ]);
        
        $irrelevantMentoringRequestStatus = implode(", ", [
            MentoringRequestStatus::CANCELLED, 
            MentoringRequestStatus::REJECTED
        ]);
        
        $sql = <<<_SQL
SELECT COUNT(*) total
FROM (
    SELECT
        MentoringRequest.id mentoringRequestId,
                MentoringRequest.requestStatus mentoringRequestStatus,
        null bookedMentoringSlotId,
        null declaredMentoringId,
                null declaredMentoringStatus,
            MentoringRequest.Participant_id participantId,
            IF(MentorReport.id IS NOT NULL AND ParticipantReport.id IS NOT NULL, true, false) reportSubmitted,
        null mentoringSlotId,
            null capacity,
            null totalBooking,
            null totalSubmittedReport,
        MentoringRequest.startTime,
        MentoringRequest.endTime,
        MentoringRequest.Consultant_id consultantId
    FROM MentoringRequest
        LEFT JOIN NegotiatedMentoring ON NegotiatedMentoring.MentoringRequest_id = MentoringRequest.id
        LEFT JOIN Mentoring ON Mentoring.id = NegotiatedMentoring.Mentoring_id
        LEFT JOIN MentorReport ON MentorReport.Mentoring_id = Mentoring.id
        LEFT JOIN ParticipantReport ON ParticipantReport.Mentoring_id = Mentoring.id
    WHERE MentoringRequest.requestStatus NOT IN ({$irrelevantMentoringRequestStatus})
            
    UNION
    SELECT
        null mentoringRequestId,
                null mentoringRequestStatus,
        null bookedMentoringSlotId,
        null declaredMentoringId,
                null declaredMentoringStatus,
            null participantId,
            null reportSubmitted,
        MentoringSlot.id mentoringSlotId,
            MentoringSlot.capacity,
            _bookedMentoringSlot.totalBooking,
            _bookedMentoringSlot.totalSubmittedReport,

        MentoringSlot.startTime,
        MentoringSlot.endTime,
        MentoringSlot.Mentor_id consultantId
    FROM MentoringSlot
        INNER JOIN Consultant ON Consultant.id = MentoringSlot.Mentor_id AND Consultant.active = true
        LEFT JOIN (
            SELECT MentoringSlot_id, COUNT(*) totalBooking, COUNT(MentorReport.id) + COUNT(ParticipantReport.id) totalSubmittedReport
            FROM BookedMentoringSlot
                LEFT JOIN Mentoring ON Mentoring.id = BookedMentoringSlot.Mentoring_id
                LEFT JOIN MentorReport ON MentorReport.Mentoring_id = Mentoring.id
                LEFT JOIN ParticipantReport ON ParticipantReport.Mentoring_id = Mentoring.id
            WHERE BookedMentoringSlot.cancelled = false
            GROUP BY MentoringSlot_id
        )_bookedMentoringSlot ON _bookedMentoringSlot.MentoringSlot_id = MentoringSlot.id
    WHERE MentoringSlot.cancelled = false
                
    UNION
    SELECT
        null mentoringRequestId,
                null mentoringRequestStatus,
        BookedMentoringSlot.id bookedMentoringSlotId,
        null declaredMentoringId,
                null declaredMentoringStatus,
            BookedMentoringSlot.Participant_id participantId,
            IF(MentorReport.id IS NOT NULL AND ParticipantReport.id IS NOT NULL, true, false) reportSubmitted,
        null mentoringSlotId,
            null capacity,
            null totalBooking,
            null totalSubmittedReport,
        MentoringSlot.startTime,
        MentoringSlot.endTime,
        MentoringSlot.Mentor_id consultantId
    FROM BookedMentoringSlot
        INNER JOIN MentoringSlot ON MentoringSlot.id = BookedMentoringSlot.MentoringSlot_id
        LEFT JOIN Mentoring ON Mentoring.id = BookedMentoringSlot.Mentoring_id
        LEFT JOIN MentorReport ON MentorReport.Mentoring_id = Mentoring.id
        LEFT JOIN ParticipantReport ON ParticipantReport.Mentoring_id = Mentoring.id
    WHERE BookedMentoringSlot.cancelled = false
                
    UNION
    SELECT
        null mentoringRequestId,
                null mentoringRequestStatus,
        null bookedMentoringSlotId,
        DeclaredMentoring.id declaredMentoringId,
                DeclaredMentoring.declaredStatus declaredMentoringStatus,
            DeclaredMentoring.Participant_id participantId,
            IF(MentorReport.id IS NOT NULL AND ParticipantReport.id IS NOT NULL, true, false) reportSubmitted,
        null mentoringSlotId,
            null capacity,
            null totalBooking,
            null totalSubmittedReport,
        DeclaredMentoring.startTime,
        DeclaredMentoring.endTime,
        DeclaredMentoring.Consultant_id consultantId
    FROM DeclaredMentoring
        LEFT JOIN Mentoring ON Mentoring.id = DeclaredMentoring.Mentoring_id
        LEFT JOIN MentorReport ON MentorReport.Mentoring_id = Mentoring.id
        LEFT JOIN ParticipantReport ON ParticipantReport.Mentoring_id = Mentoring.id
    WHERE DeclaredMentoring.declaredStatus NOT IN ({$irrelevantDeclaredMentoringStatus})
    
)_mentoring
    INNER JOIN Consultant ON Consultant.id = _mentoring.consultantId
    
    INNER JOIN Coordinator
        ON Coordinator.Program_id = Consultant.Program_id
        AND Coordinator.Personnel_id = :personnelId
        AND Coordinator.active = true
    
WHERE 1
    {$filter->getCriteriaStatement($parameters)}
_SQL;
        $query = $this->em->getConnection()->prepare($sql);
        return $query->executeQuery($parameters)->fetchFirstColumn()[0];
    }

    public function summaryOfMentoringBelongsToPersonnel(string $personnelId)
    {
        $parameters = [
            'personnelId' => $personnelId,
        ];
        $irrelevantDeclaredMentoringStatus = implode(", ", [
            DeclaredMentoringStatus::CANCELLED, 
            DeclaredMentoringStatus::DENIED_BY_MENTOR, 
            DeclaredMentoringStatus::DENIED_BY_PARTICIPANT
        ]);
        
        $irrelevantMentoringRequestStatus = implode(", ", [
            MentoringRequestStatus::CANCELLED, 
            MentoringRequestStatus::REJECTED
        ]);
        $ongoingMentoringRequestStatus = implode(", ", [
            MentoringRequestStatus::OFFERED, 
            MentoringRequestStatus::REQUESTED
        ]);
        $confirmedMentoringRequestStatus = implode(", ", [
            MentoringRequestStatus::ACCEPTED_BY_PARTICIPANT, 
            MentoringRequestStatus::APPROVED_BY_MENTOR
        ]);
        
        $currentTime = "'". (new \DateTime())->format('Y-m-d H:i:s') . "'";
        
        $sql = <<<_SQL
SELECT 
    COUNT(IF(
        startTime >= {$currentTime} AND capacity > COALESCE(totalBooking, 0) AND capacity >= 1,
        mentoringSlotId, null
    )) availableSlot,
    COUNT(IF(
        mentoringRequestStatus IN ({$ongoingMentoringRequestStatus}),
        mentoringRequestId, null
    )) ongoingMentoringRequest,
    (
        COUNT(IF(
            startTime >= {$currentTime} AND mentoringRequestStatus IN ({$confirmedMentoringRequestStatus}),
            mentoringRequestId, null
        ))
        + COUNT(IF(
            startTime >= {$currentTime} AND totalBooking IS NOT NULL,
            mentoringSlotId, null
        ))
    ) confirmedMentoring,
    (
        COUNT(IF(
            endTime <= {$currentTime} AND reportSubmitted = true,
            IF(mentoringRequestStatus IN ({$confirmedMentoringRequestStatus}), mentoringRequestId, declaredMentoringId),
            null
        ))
        + COUNT(IF(
            endTime <= {$currentTime} AND totalBooking IS NOT NULL AND totalSubmittedReport = totalBooking,
            mentoringSlotId, null
        ))
    ) completedMentoring,
    (
        COUNT(IF(
            endTime <= {$currentTime} AND (reportSubmitted IS NULL OR reportSubmitted = false),
            IF(mentoringRequestStatus IN ({$confirmedMentoringRequestStatus}), mentoringRequestId, declaredMentoringId),
            null
        ))
        + COUNT(IF(
            endTime <= {$currentTime} AND totalBooking IS NOT NULL AND totalSubmittedReport < totalBooking,
            mentoringSlotId, null
        ))
    ) incompleteReportMentoring
FROM (
    SELECT
        MentoringRequest.id mentoringRequestId,
                MentoringRequest.requestStatus mentoringRequestStatus,
        null declaredMentoringId,
                null declaredMentoringStatus,
            MentoringRequest.Participant_id participantId,
            IF(MentorReport.id IS NOT NULL, true, false) reportSubmitted,
        null mentoringSlotId,
            null capacity,
            null totalBooking,
            null totalSubmittedReport,
        MentoringRequest.startTime,
        MentoringRequest.endTime,
        MentoringRequest.Consultant_id consultantId
    FROM MentoringRequest
        LEFT JOIN NegotiatedMentoring ON NegotiatedMentoring.MentoringRequest_id = MentoringRequest.id
        LEFT JOIN Mentoring ON Mentoring.id = NegotiatedMentoring.Mentoring_id
        LEFT JOIN MentorReport ON MentorReport.Mentoring_id = Mentoring.id
    WHERE MentoringRequest.requestStatus NOT IN ({$irrelevantMentoringRequestStatus})
            
    UNION
    SELECT
        null mentoringRequestId,
                null mentoringRequestStatus,
        null declaredMentoringId,
                null declaredMentoringStatus,
            null participantId,
            null reportSubmitted,
        MentoringSlot.id mentoringSlotId,
            MentoringSlot.capacity,
            _bookedMentoringSlot.totalBooking,
            _bookedMentoringSlot.totalSubmittedReport,

        MentoringSlot.startTime,
        MentoringSlot.endTime,
        MentoringSlot.Mentor_id consultantId
    FROM MentoringSlot
        INNER JOIN Consultant ON Consultant.id = MentoringSlot.Mentor_id AND Consultant.active = true
        LEFT JOIN (
            SELECT MentoringSlot_id, COUNT(*) totalBooking, COUNT(MentorReport.id) totalSubmittedReport
            FROM BookedMentoringSlot
                LEFT JOIN Mentoring ON Mentoring.id = BookedMentoringSlot.Mentoring_id
                LEFT JOIN MentorReport ON MentorReport.Mentoring_id = Mentoring.id
            WHERE BookedMentoringSlot.cancelled = false
            GROUP BY MentoringSlot_id
        )_bookedMentoringSlot ON _bookedMentoringSlot.MentoringSlot_id = MentoringSlot.id
    WHERE MentoringSlot.cancelled = false
                
    UNION
    SELECT
        null mentoringRequestId,
                null mentoringRequestStatus,
        DeclaredMentoring.id declaredMentoringId,
                DeclaredMentoring.declaredStatus declaredMentoringStatus,
            DeclaredMentoring.Participant_id participantId,
            IF(MentorReport.id IS NOT NULL, true, false) reportSubmitted,
        null mentoringSlotId,
            null capacity,
            null totalBooking,
            null totalSubmittedReport,
        DeclaredMentoring.startTime,
        DeclaredMentoring.endTime,
        DeclaredMentoring.Consultant_id consultantId
    FROM DeclaredMentoring
        LEFT JOIN Mentoring ON Mentoring.id = DeclaredMentoring.Mentoring_id
        LEFT JOIN MentorReport ON MentorReport.Mentoring_id = Mentoring.id
    WHERE DeclaredMentoring.declaredStatus NOT IN ({$irrelevantDeclaredMentoringStatus})
    
)_mentoring
    INNER JOIN Consultant 
        ON Consultant.id = _mentoring.consultantId
        AND Consultant.Personnel_id = :personnelId
_SQL;
        $query = $this->em->getConnection()->prepare($sql);
        return $query->executeQuery($parameters)->fetchAllAssociative();
    }

}
