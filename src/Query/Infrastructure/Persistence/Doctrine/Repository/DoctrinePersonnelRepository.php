<?php

namespace Query\Infrastructure\Persistence\Doctrine\Repository;

use Doctrine\ORM\EntityRepository;
use Doctrine\ORM\NoResultException;
use PDO;
use Query\Application\Auth\Firm\PersonnelRepository as InterfaceForAuthorization;
use Query\Application\Service\Firm\PersonnelRepository;
use Query\Application\Service\Personnel\PersonnelRepository as InterfaceForPersonnel;
use Query\Domain\Model\Firm\Personnel;
use Query\Domain\Task\Dependency\Firm\PersonnelRepository as PersonnelRepository2;
use Resources\Exception\RegularException;
use Resources\Infrastructure\Persistence\Doctrine\PaginatorBuilder;
use SharedContext\Domain\ValueObject\DeclaredMentoringStatus;
use SharedContext\Domain\ValueObject\MentoringRequestStatus;
use SharedContext\Domain\ValueObject\RegistrationStatus;
use SharedContext\Domain\ValueObject\TaskReportReviewStatus;

class DoctrinePersonnelRepository extends EntityRepository implements PersonnelRepository, InterfaceForAuthorization, InterfaceForPersonnel,
        PersonnelRepository2
{

    public function ofId(string $firmId, string $personnelId): Personnel
    {
        $qb = $this->createQueryBuilder('personnel');
        $qb->select('personnel')
                ->leftJoin('personnel.firm', 'firm')
                ->andWhere($qb->expr()->eq('firm.id', ':firmId'))
                ->andWhere($qb->expr()->eq('personnel.id', ':personnelId'))
                ->setParameter('firmId', $firmId)
                ->setParameter('personnelId', $personnelId)
                ->setMaxResults(1);
        try {
            return $qb->getQuery()->getSingleResult();
        } catch (NoResultException $ex) {
            $errorDetail = "not found: personnel not found";
            throw RegularException::notFound($errorDetail);
        }
    }

    public function all(string $firmId, int $page, int $pageSize, ?bool $activeStatus)
    {
        $qb = $this->createQueryBuilder('personnel');
        $qb->select('personnel')
                ->leftJoin('personnel.firm', 'firm')
                ->andWhere($qb->expr()->eq('firm.id', ':firmId'))
                ->setParameter('firmId', $firmId);

        if (isset($activeStatus)) {
            $qb->andWhere($qb->expr()->eq("personnel.active", ":activeStatus"))
                    ->setParameter("activeStatus", $activeStatus);
        }

        return PaginatorBuilder::build($qb->getQuery(), $page, $pageSize);
    }

    public function ofEmail(string $firmIdentifier, string $email): Personnel
    {
        $parameters = [
            "email" => $email,
            "firmIdentifier" => $firmIdentifier,
        ];

        $qb = $this->createQueryBuilder('personnel');
        $qb->select('personnel')
                ->andWhere($qb->expr()->eq('personnel.active', 'true'))
                ->andWhere($qb->expr()->eq('personnel.email', ':email'))
                ->leftJoin('personnel.firm', 'firm')
                ->andWhere($qb->expr()->eq('firm.identifier', ':firmIdentifier'))
                ->setParameters($parameters)
                ->setMaxResults(1);
        try {
            return $qb->getQuery()->getSingleResult();
        } catch (NoResultException $ex) {
            $errorDetail = "not found: personnel not found";
            throw RegularException::notFound($errorDetail);
        }
    }

    public function containRecordOfActivePersonnelInFirm(string $firmId, string $personnelId): bool
    {
        $params = [
            "firmId" => $firmId,
            "personnelId" => $personnelId,
        ];

        $qb = $this->createQueryBuilder("personnel");
        $qb->select("1")
                ->andWhere($qb->expr()->eq("personnel.id", ":personnelId"))
                ->andWhere($qb->expr()->eq("personnel.active", "true"))
                ->leftJoin("personnel.firm", "firm")
                ->andWhere($qb->expr()->eq("firm.id", ":firmId"))
                ->setParameters($params)
                ->setMaxResults(1);

        return !empty($qb->getQuery()->getResult());
    }

    public function aPersonnelInFirm(string $firmId, string $personnelId): Personnel
    {
        $params = [
            'firmId' => $firmId,
            'personnelId' => $personnelId,
        ];
        $qb = $this->createQueryBuilder('personnel');
        $qb->select('personnel')
                ->andWhere($qb->expr()->eq('personnel.id', ':personnelId'))
                ->leftJoin('personnel.firm', 'firm')
                ->andWhere($qb->expr()->eq('firm.id', ':firmId'))
                ->setParameters($params)
                ->setMaxResults(1);
        try {
            return $qb->getQuery()->getSingleResult();
        } catch (NoResultException $ex) {
            $errorDetail = "not found: personnel not found";
            throw RegularException::notFound($errorDetail);
        }
    }

    public function mentorDashboardSummary(string $personnelId)
    {
        $approvedTaskReportStatus = TaskReportReviewStatus::APPROVED;
        $negotiatingMentoringRequest = implode(', ', [
                MentoringRequestStatus::OFFERED,
                MentoringRequestStatus::REQUESTED,
        ]);
        $confirmedDeclaredMentoringStatus = implode(", ", [
                DeclaredMentoringStatus::APPROVED_BY_MENTOR,
                DeclaredMentoringStatus::APPROVED_BY_PARTICIPANT,
       ]);
        $statement = <<<_STATEMENT
SELECT 
    Consultant.Personnel_id as personnelId,
    SUM(_a.unrespondedMentoringRequest) as unrespondedMentoringRequest,
    SUM(_b.pendingActivityReport) as pendingActivityReport,
    COALESCE(SUM(_c.pendingNegotiatedMentoringReport), 0) 
        + COALESCE(SUM(_d.pendingMentoringSlotReport), 0) 
        + COALESCE(SUM(_declaredMentoring.pendingDeclaredMentoringReport), 0) 
        as pendingMentoringReport,
    SUM(_e.newWorksheetSubmission) as newWorksheetSubmission,
    SUM(_f.incompleteTask) as incompleteTask
FROM Consultant
LEFT JOIN (
    SELECT MentoringRequest.consultant_id as consultantId, COUNT(MentoringRequest.id) as unrespondedMentoringRequest
    FROM MentoringRequest
    WHERE MentoringRequest.requestStatus IN ({$negotiatingMentoringRequest})
    GROUP BY consultantId
)_a ON _a.consultantId = Consultant.id
LEFT JOIN (
    SELECT ConsultantInvitee.Consultant_id as consultantId, COUNT(ConsultantInvitee.id) as pendingActivityReport
    FROM ConsultantInvitee
    INNER JOIN Invitee ON Invitee.id = ConsultantInvitee.Invitee_id AND Invitee.cancelled = false
    INNER JOIN Activity ON Activity.id = Invitee.Activity_id AND Activity.endDateTime < NOW()
    WHERE 
        NOT EXISTS (
            SELECT 1
            FROM InviteeReport
            WHERE Invitee_id = Invitee.id
        )
    GROUP BY consultantId
)_b ON _b.consultantId = Consultant.id
LEFT JOIN (
    SELECT
        MentoringRequest.Consultant_id as consultantId, 
        COUNT(NegotiatedMentoring.id) as pendingNegotiatedMentoringReport
    FROM MentoringRequest
    INNER JOIN NegotiatedMentoring ON NegotiatedMentoring.MentoringRequest_id = MentoringRequest.id
    WHERE 
        MentoringRequest.endTime < NOW()
        AND NOT EXISTS (
            SELECT 1
            FROM MentorReport
            WHERE Mentoring_id = NegotiatedMentoring.Mentoring_id
        )
    GROUP BY consultantId
)_c ON _c.consultantId = Consultant.id
LEFT JOIN (
    SELECT 
        MentoringSlot.Mentor_id consultantId, COUNT(*) pendingMentoringSlotReport
    FROM MentoringSlot
        LEFT JOIN (
            SELECT MentoringSlot_id, COUNT(*) totalBooking, COUNT(MentorReport.id) totalSubmittedReport
            FROM BookedMentoringSlot
                LEFT JOIN Mentoring ON Mentoring.id = BookedMentoringSlot.Mentoring_id
                LEFT JOIN MentorReport ON MentorReport.Mentoring_id = Mentoring.id
            WHERE BookedMentoringSlot.cancelled = false
            GROUP BY MentoringSlot_id
        )_bookedMentoringSlot ON _bookedMentoringSlot.MentoringSlot_id = MentoringSlot.id
    WHERE MentoringSlot.cancelled = false
        AND _bookedMentoringSlot.totalSubmittedReport < _bookedMentoringSlot.totalBooking
        AND _bookedMentoringSlot.totalBooking IS NOT NULL
    GROUP BY consultantId
)_d ON _d.consultantId = Consultant.id
LEFT JOIN (
    SELECT
        DeclaredMentoring.Consultant_id consultantId,
        COUNT(*) pendingDeclaredMentoringReport
    FROM DeclaredMentoring
    WHERE declaredStatus IN ({$confirmedDeclaredMentoringStatus})
        AND NOT EXISTS (
            SELECT 1
            FROM MentorReport
            WHERE Mentoring_id = DeclaredMentoring.Mentoring_id
        )
    GROUP BY consultantId
)_declaredMentoring ON _declaredMentoring.consultantId = Consultant.id
LEFT JOIN (
    SELECT DedicatedMentor.Consultant_id as consultantId, COUNT(Worksheet.id) as newWorksheetSubmission
    FROM DedicatedMentor
    INNER JOIN Participant ON Participant.id = DedicatedMentor.Participant_id AND Participant.active = true
    LEFT JOIN Worksheet ON Worksheet.Participant_id = Participant.id AND Worksheet.removed = false
    WHERE DedicatedMentor.cancelled = false
        AND NOT EXISTS (
            SELECT 1
            FROM ConsultantComment
            INNER JOIN Comment ON Comment.id = ConsultantComment.Comment_id
            WHERE Comment.Worksheet_id = Worksheet.id 
                AND ConsultantComment.Consultant_id = DedicatedMentor.Consultant_id
        )
    GROUP BY consultantId
)_e ON _e.consultantId = Consultant.id
LEFT JOIN (
    SELECT
        Consultant.id consultantId, COUNT(*) incompleteTask
    FROM Task
        LEFT JOIN TaskReport ON TaskReport.Task_id = Task.id
        INNER JOIN Participant ON Participant.id = Task.Participant_id
        LEFT JOIN DedicatedMentor 
            ON DedicatedMentor.Participant_id = Participant.id
            AND DedicatedMentor.cancelled = false
        LEFT JOIN ConsultantTask ON ConsultantTask.Task_id = Task.id
        INNER JOIN Consultant ON Consultant.id = ConsultantTask.Consultant_id OR Consultant.id = DedicatedMentor.Consultant_id
    WHERE Task.cancelled = false
        AND (
            TaskReport.id IS NULL 
            OR TaskReport.reviewStatus <> {$approvedTaskReportStatus}
        )
    GROUP BY consultantId
)_f ON _f.consultantId = Consultant.id
WHERE Consultant.active = true
    AND Consultant.Personnel_id = :personnelId
GROUP BY personnelId
_STATEMENT;
        $query = $this->getEntityManager()->getConnection()->prepare($statement);
        $params = ["personnelId" => $personnelId];
        $query->execute($params);

        return $query->fetchAll(PDO::FETCH_ASSOC);
    }

    public function coordinatorDashboardSummary(string $personnelId)
    {
        $approvedTaskReportStatus = TaskReportReviewStatus::APPROVED;
        $mentoringRequestProposedByParticipant = MentoringRequestStatus::REQUESTED;
        $mentoringRequestOfferedByMentor = MentoringRequestStatus::OFFERED;
        $newApplicantStatus = RegistrationStatus::REGISTERED;
        $statement = <<<_STATEMENT
SELECT 
    SUM(_a.newApplicantInProgramCount) newApplicantCount,
    SUM(_b.uncommentedWorksheetCount) uncommentedWorksheetCount,
    SUM(_b.unconcludedMentoringRequestCount) unconcludedMentoringRequestCount,
    SUM(_b.unreviewedMetricReportCount) unreviewedMetricReportCount,
    SUM(_c.incompleteTask) incompleteTask
FROM Coordinator
LEFT JOIN (
    SELECT COUNT(Registrant.id) newApplicantInProgramCount, Registrant.Program_id programId
    FROM Registrant
    WHERE Registrant.status = {$newApplicantStatus}
    GROUP BY programId
)_a ON _a.programId = Coordinator.Program_id
LEFT JOIN (
    SELECT
        SUM(_b1.uncommentedWorksheetCount) uncommentedWorksheetCount,
        SUM(_b2.unconcludedMentoringRequestCount) unconcludedMentoringRequestCount,
        SUM(_b3.unreviewedMetricReportCount) unreviewedMetricReportCount,
        Participant.Program_id programId
    FROM Participant
    LEFT JOIN (
        SELECT COUNT(w.id) uncommentedWorksheetCount, w.Participant_id participantId
        FROM Worksheet as w
        WHERE w.removed = false
            AND NOT EXISTS (
                SELECT 1
                FROM ConsultantComment
                INNER JOIN Comment ON Comment.id = ConsultantComment.Comment_id
                WHERE Comment.Worksheet_id = w.id
            )
        GROUP BY participantId
    )_b1 ON _b1.participantId = Participant.id
    LEFT JOIN (
        SELECT COUNT(MentoringRequest.id) unconcludedMentoringRequestCount, MentoringRequest.Participant_id participantId
        FROM MentoringRequest
        WHERE MentoringRequest.requestStatus IN ({$mentoringRequestProposedByParticipant}, {$mentoringRequestOfferedByMentor})
        GROUP BY participantId
    )_b2 ON _b2.participantId = Participant.id
    LEFT JOIN (
        SELECT COUNT(MetricAssignmentReport.id) unreviewedMetricReportCount, MetricAssignment.Participant_id participantId
        FROM MetricAssignmentReport
        INNER JOIN MetricAssignment ON MetricAssignment.id  = MetricAssignmentReport.MetricAssignment_id
        WHERE MetricAssignmentReport.removed = false
            AND MetricAssignmentReport.approved IS NULL
        GROUP BY participantId
    )_b3 ON _b3.participantId = Participant.id
    WHERE Participant.active = true
    GROUP BY programId
)_b ON _b.programId = Coordinator.Program_id
LEFT JOIN (
    SELECT
        Participant.Program_id programId, COUNT(*) incompleteTask
    FROM Task
        LEFT JOIN TaskReport ON TaskReport.Task_id = Task.id
        INNER JOIN Participant ON Participant.id = Task.Participant_id
    WHERE Task.cancelled = false 
        AND (
            TaskReport.id IS NULL 
            OR TaskReport.reviewStatus <> {$approvedTaskReportStatus}
        )
    GROUP BY programId
)_c ON _c.programId = Coordinator.Program_id
WHERE Coordinator.active = true
    AND Coordinator.Personnel_id = :personnelId
_STATEMENT;
        $query = $this->getEntityManager()->getConnection()->prepare($statement);
        $parameters = [
            'personnelId' => $personnelId,
        ];
        return $query->executeQuery($parameters)->fetchAllAssociative();
    }

}
