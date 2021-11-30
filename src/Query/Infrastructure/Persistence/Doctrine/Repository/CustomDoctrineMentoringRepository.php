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
        {$filter->getMentoringSlotFilter()->getSqlReportCompletedStatusClause('_u1b.submittedReportCount', '_u1b.bookedSlotCount')}
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
        {$filter->getMentoringSlotFilter()->getSqlReportCompletedStatusClause('_u1b.submittedReportCount', '_u1b.bookedSlotCount')}
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
        
        $fromClause = '';
        $from = $filter->getFrom();
        if (isset($from)) {
            $fromClause = 'AND MentoringSlot.startTime > :from';
            $parameters['from'] = $from->format('Y-m-d H:i:s');
        }
        
        $toClause = '';
        $to = $filter->getTo();
        if (isset($to)) {
            $toClause = 'AND MentoringSlot.endTime < :to';
            $parameters['to'] = $to->format('Y-m-d H:i:s');
        }
        
        $mentoringSlotFilter = $filter->getMentoringSlotFilter();
        $cancelledClause = '';
        $reportCompletedClause = '';
        if (isset($mentoringSlotFilter)) {
            $cancelledStatus = $mentoringSlotFilter->getCancelledStatus();
            if (isset($cancelledStatus)) {
                $cancelledClause = 'AND BookedMentoringSlot.cancelled = :cancelledStatus';
                $parameters['cancelledStatus'] = $cancelledStatus;
            }
            
            $reportCompletedStatus = $mentoringSlotFilter->getReportCompletedStatus();
            if ($reportCompletedStatus === true) {
                $reportCompletedClause = 'AND ParticipantReport.id IS NOT NULL';
            } elseif ($reportCompletedStatus === false) {
                $reportCompletedClause = 'AND ParticipantReport.id IS NULL';
            }
        }
        
        $statement = <<<_STATEMENT
SELECT
    BookedMentoringSlot.id bookedMentoringSlotId,
    ParticipantReport.id reportId,
    BookedMentoringSlot.cancelled,
    MentoringSlot.startTime,
    MentoringSlot.endTime,
    Consultant.id mentorId,
    CONCAT(Personnel.firstName, ' ', COALESCE(Personnel.lastName, '')) mentorName,
    Program.id programId,
    Program.name programName,
    ConsultationSetup.id consultationSetupId,
    ConsultationSetup.name consultationSetupName
FROM BookedMentoringSlot
LEFT JOIN Participant ON Participant.id = BookedMentoringSlot.Participant_id
LEFT JOIN Program ON Program.id = Participant.Program_id
LEFT JOIN MentoringSlot ON MentoringSlot.id = BookedMentoringSlot.MentoringSlot_id
LEFT JOIN Consultant ON Consultant.id = MentoringSlot.Mentor_id
LEFT JOIN Personnel ON Personnel.id = Consultant.Personnel_id
LEFT JOIN ConsultationSetup ON ConsultationSetup.id = MentoringSlot.ConsultationSetup_id
LEFT JOIN ParticipantReport ON ParticipantReport.Mentoring_id = BookedMentoringSlot.Mentoring_id
WHERE Participant.id = :participantId
{$fromClause}
{$toClause}
{$cancelledClause}
{$reportCompletedClause}
ORDER BY MentoringSlot.startTime {$filter->getOrderDirection()}
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
        $params = [
            "participantId" => $participantId,
        ];
        
        $fromClause = '';
        $from = $filter->getFrom();
        if (isset($from)) {
            $fromClause = 'AND MentoringSlot.startTime > :from';
            $params['from'] = $from->format('Y-m-d H:i:s');
        }
        
        $toClause = '';
        $to = $filter->getTo();
        if (isset($to)) {
            $toClause = 'AND MentoringSlot.endTime < :to';
            $params['to'] = $to->format('Y-m-d H:i:s');
        }
        
        $mentoringSlotFilter = $filter->getMentoringSlotFilter();
        $cancelledClause = '';
        $reportCompletedClause = '';
        if (isset($mentoringSlotFilter)) {
            $cancelledStatus = $mentoringSlotFilter->getCancelledStatus();
            if (isset($cancelledStatus)) {
                $cancelledClause = 'AND BookedMentoringSlot.cancelled = :cancelledStatus';
                $params['cancelledStatus'] = $cancelledStatus;
            }
            
            $reportCompletedStatus = $mentoringSlotFilter->getReportCompletedStatus();
            if ($reportCompletedStatus === true) {
                $reportCompletedClause = 'AND ParticipantReport.id IS NOT NULL';
            } elseif ($reportCompletedStatus === false) {
                $reportCompletedClause = 'AND ParticipantReport.id IS NULL';
            }
        }
        
        $statement = <<<_STATEMENT
SELECT COUNT(BookedMentoringSlot.id) total
FROM BookedMentoringSlot
LEFT JOIN Participant ON Participant.id = BookedMentoringSlot.Participant_id
LEFT JOIN MentoringSlot ON MentoringSlot.id = BookedMentoringSlot.MentoringSlot_id
LEFT JOIN ParticipantReport ON ParticipantReport.Mentoring_id = BookedMentoringSlot.Mentoring_id
WHERE Participant.id = :participantId
{$fromClause}
{$toClause}
{$cancelledClause}
{$reportCompletedClause}
_STATEMENT;

        $query = $this->em->getConnection()->prepare($statement);
        return $query->executeQuery($params)->fetchFirstColumn()[0];
    }
    
    protected function mentoringRequestQuery(string $personnelId, MentoringFilter $filter)
    {
        $offset = $pageSize * ($page - 1);

        $parameters = [
            "personnelId" => $personnelId,
        ];

        $fromClause = '';
        $from = $filter->getFrom();
        if (isset($from)) {
            $fromClause = 'AND MentoringSlot.startTime > :from';
            $params['from'] = $from->format('Y-m-d H:i:s');
        }

        $toClause = '';
        $to = $filter->getTo();
        if (isset($to)) {
            $toClause = 'AND MentoringSlot.endTime < :to';
            $params['to'] = $to->format('Y-m-d H:i:s');
        }

        $mentoringRequestFilter = $filter->getMentoringRequestFilter();
        if (!emp) {
            
        }
        
        $statusInClause = '';
        $reportCompletedClause = '';
        
        $statement = <<<_STATEMENT
SELECT 
    _ba.mentoringRequestId,
    _bb.id negotiatedMentoringId,
    _bc.id mentorReportId,
    _ba.startTime,
    _ba.endTime,
    _ba.requestStatus,
    _ba.participantId,
    COALESCE(_bd.userName, _be.clientName, _bf.teamName) participantName,
    _ba.mentorId,
    _ba.programId,
    _ba.programName,
    _ba.consultationSetupId,
    _ba.consultationSetupName
FROM (
    SELECT 
        MentoringRequest.id mentoringRequestId,
        MentoringRequest.startTime,
        MentoringRequest.endTime,
        MentoringRequest.requestStatus,
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
)_ba
LEFT JOIN (
    SELECT id, Mentoring_id, MentoringRequest_id
    FROM NegotiatedMentoring
)_bb ON _bb.MentoringRequest_id = _ba.mentoringRequestId
LEFT JOIN (
    SELECT id, Mentoring_id
    FROM MentorReport
)_bc ON _bc.Mentoring_id = _bb.Mentoring_id
LEFT JOIN (
    SELECT CONCAT(User.firstName, ' ', COALESCE(User.lastName, '')) userName, UserParticipant.Participant_id participantId
    FROM UserParticipant
        LEFT JOIN User ON User.id = UserParticipant.User_id
)_bd ON _bd.participantId = _ba.participantId
LEFT JOIN (
    SELECT CONCAT(Client.firstName, ' ', COALESCE(Client.lastName, '')) clientName, ClientParticipant.Participant_id participantId
    FROM ClientParticipant
        LEFT JOIN Client ON Client.id = ClientParticipant.Client_id
)_be ON _be.participantId = _ba.participantId
LEFT JOIN (
    SELECT Team.name teamName, TeamParticipant.Participant_id participantId
    FROM TeamParticipant
        LEFT JOIN Team ON Team.id = TeamParticipant.Team_id
)_bf ON _bf.participantId = _ba.participantId
_STATEMENT;
    }

}
