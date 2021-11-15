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
        
        $params = [
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
        
        $mentoringSlotFilter = $filter->getMentoringSlotFilter();
        $bookingAvailableClause = '';
        $cancelledClause = '';
        $reportCompletedClause = '';
        if (isset($mentoringSlotFilter)) {
            $bookingAvailableStatus = $mentoringSlotFilter->getBookingAvailableStatus();
            if ($bookingAvailableStatus === true) {
                $bookingAvailableClause = 'WHERE _b.bookedSlotCount < _a.capacity';
            } elseif ($bookingAvailableStatus === false) {
                $bookingAvailableClause = 'WHERE _b.bookedSlotCount = _a.capacity';
            }
            
            $cancelledStatus = $mentoringSlotFilter->getCancelledStatus();
            if (isset($cancelledStatus)) {
                $cancelledClause = empty($bookingAvailableClause) ?
                        'WHERE _a.cancelled = :cancelledStatus' :
                        'AND _a.cancelled = :cancelledStatus';
                $params['cancelledStatus'] = $cancelledStatus;
            }
            
            $reportCompletedStatus = $mentoringSlotFilter->getReportCompletedStatus();
            if ($reportCompletedStatus === true) {
                $reportCompletedClause = (empty($bookingAvailableClause) && empty($cancelledClause)) ?
                        'WHERE _b.submittedReportCount = _b.bookedSlotCount':
                        'AND _b.submittedReportCount = _b.bookedSlotCount';
            } elseif ($reportCompletedStatus === false) {
                $reportCompletedClause = (empty($bookingAvailableClause) && empty($cancelledClause)) ?
                        'WHERE _b.submittedReportCount < _b.bookedSlotCount':
                        'AND _b.submittedReportCount < _b.bookedSlotCount';
            }
        }
        
        $statement = <<<_STATEMENT
SELECT 
    _a.mentoringSlotId,
    _b.bookedSlotCount,
    _a.capacity,
    _b.submittedReportCount,
    _a.cancelled,
    _a.startTime,
    _a.endTime,
    _a.mentorId,
    _a.programId,
    _a.programName,
    _a.consultationSetupId,
    _a.consultationSetupName
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
        {$fromClause}
        {$toClause}
)_a
LEFT JOIN (
    SELECT
        BookedMentoringSlot.MentoringSlot_id mentoringSlotId,
        COUNT(BookedMentoringSlot.id) bookedSlotCount,
        COUNT(MentorReport.id) submittedReportCount
    FROM BookedMentoringSlot
    LEFT JOIN MentorReport ON MentorReport.Mentoring_id = BookedMentoringSlot.Mentoring_id
    GROUP BY mentoringSlotId
)_b ON _b.mentoringSlotId = _a.mentoringSlotId
{$bookingAvailableClause}
{$cancelledClause}
{$reportCompletedClause}
ORDER BY _a.startTime {$filter->getOrderDirection()}
LIMIT {$offset}, {$pageSize}
_STATEMENT;
        $query = $this->em->getConnection()->prepare($statement);
//        $query->execute($params);
//        return $query->fetchAll(PDO::FETCH_ASSOC);
        return [
            'total' => $this->totalCountOfAllMentoringsBelongsToPersonnel($personnelId, $filter),
            'list' => $query->executeQuery($params)->fetchAllAssociative(),
        ];
    }
    
    protected function totalCountOfAllMentoringsBelongsToPersonnel(string $personnelId, MentoringFilter $filter)
    {
        $params = [
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
        
        $mentoringSlotFilter = $filter->getMentoringSlotFilter();
        $bookingAvailableClause = '';
        $cancelledClause = '';
        $reportCompletedClause = '';
        if (isset($mentoringSlotFilter)) {
            $bookingAvailableStatus = $mentoringSlotFilter->getBookingAvailableStatus();
            if ($bookingAvailableStatus === true) {
                $bookingAvailableClause = 'WHERE _b.bookedSlotCount < _a.capacity';
            } elseif ($bookingAvailableStatus === false) {
                $bookingAvailableClause = 'WHERE _b.bookedSlotCount = _a.capacity';
            }
            
            $cancelledStatus = $mentoringSlotFilter->getCancelledStatus();
            if (isset($cancelledStatus)) {
                $cancelledClause = empty($bookingAvailableClause) ?
                        'WHERE _a.cancelled = :cancelledStatus' :
                        'AND _a.cancelled = :cancelledStatus';
                $params['cancelledStatus'] = $cancelledStatus;
            }
            
            $reportCompletedStatus = $mentoringSlotFilter->getReportCompletedStatus();
            if ($reportCompletedStatus === true) {
                $reportCompletedClause = (empty($bookingAvailableClause) && empty($cancelledClause)) ?
                        'WHERE _b.submittedReportCount = _b.bookedSlotCount':
                        'AND _b.submittedReportCount = _b.bookedSlotCount';
            } elseif ($reportCompletedStatus === false) {
                $reportCompletedClause = (empty($bookingAvailableClause) && empty($cancelledClause)) ?
                        'WHERE _b.submittedReportCount < _b.bookedSlotCount':
                        'AND _b.submittedReportCount < _b.bookedSlotCount';
            }
        }
        
        $statement = <<<_STATEMENT
SELECT COUNT(_a.mentoringSlotId) total
FROM (
    SELECT
        MentoringSlot.id mentoringSlotId,
        MentoringSlot.capacity,
        MentoringSlot.cancelled,
        MentoringSlot.startTime,
        MentoringSlot.endTime
    FROM MentoringSlot
    LEFT JOIN Consultant on Consultant.id = MentoringSlot.Mentor_id
    LEFT JOIN Program on Program.id = Consultant.Program_id
    LEFT JOIN ConsultationSetup on ConsultationSetup.id = MentoringSlot.ConsultationSetup_id
    WHERE Consultant.Personnel_id = :personnelId
        AND Consultant.active = true
        {$fromClause}
        {$toClause}
)_a
LEFT JOIN (
    SELECT
        BookedMentoringSlot.MentoringSlot_id mentoringSlotId,
        COUNT(BookedMentoringSlot.id) bookedSlotCount,
        COUNT(MentorReport.id) submittedReportCount
    FROM BookedMentoringSlot
    LEFT JOIN MentorReport ON MentorReport.Mentoring_id = BookedMentoringSlot.Mentoring_id
    GROUP BY mentoringSlotId
)_b ON _b.mentoringSlotId = _a.mentoringSlotId
{$bookingAvailableClause}
{$cancelledClause}
{$reportCompletedClause}
_STATEMENT;

        $query = $this->em->getConnection()->prepare($statement);
        return $query->executeQuery($params)->fetchFirstColumn()[0];
        
    }

}
