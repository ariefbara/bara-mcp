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
        $statement = <<<_STATEMENT
SELECT 
    Consultant.Personnel_id as personnelId,
    SUM(_a.unrespondedMentoringRequest) as unrespondedMentoringRequest,
    SUM(_b.pendingActivityReport) as pendingActivityReport,
    SUM(_c.pendingNegotiatedMentoringReport) + SUM(_d.pendingBookedMentoringSlotReport) as pendingMentoringReport,
    SUM(_e.newWorksheetSubmission) as newWorksheetSubmission
FROM Consultant
LEFT JOIN (
    SELECT MentoringRequest.consultant_id as consultantId, COUNT(MentoringRequest.id) as unrespondedMentoringRequest
    FROM MentoringRequest
    INNER JOIN Participant ON Participant.id = MentoringRequest.Participant_id AND Participant.active = true
    WHERE MentoringRequest.requestStatus = 0
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
    FROM NegotiatedMentoring
    INNER JOIN MentoringRequest 
        ON MentoringRequest.id = NegotiatedMentoring.MentoringRequest_id AND MentoringRequest.endTime < NOW()
    INNER JOIN Participant ON Participant.id = MentoringRequest.Participant_id AND Participant.active = true
    WHERE 
        NOT EXISTS (
            SELECT 1
            FROM MentorReport
            WHERE Mentoring_id = NegotiatedMentoring.Mentoring_id
        )
    GROUP BY consultantId
)_c ON _c.consultantId = Consultant.id
LEFT JOIN (
    SELECT 
        MentoringSlot.Mentor_id as consultantId, 
        COUNT(BookedMentoringSlot.id) as pendingBookedMentoringSlotReport
    FROM BookedMentoringSlot
    INNER JOIN MentoringSlot 
        ON MentoringSlot.id = BookedMentoringSlot.MentoringSlot_id AND MentoringSlot.endTime < NOW()
    INNER JOIN Participant ON Participant.id = BookedMentoringSlot.Participant_id AND Participant.active = true
    WHERE 
        NOT EXISTS (
            SELECT 1
            FROM MentorReport
            WHERE Mentoring_id = BookedMentoringSlot.Mentoring_id
        )
    GROUP BY consultantId
)_d ON _d.consultantId = Consultant.id
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
WHERE Consultant.active = true
    AND Consultant.Personnel_id = :personnelId
GROUP BY personnelId
_STATEMENT;
        $query = $this->getEntityManager()->getConnection()->prepare($statement);
        $params = ["personnelId" => $personnelId];
        $query->execute($params);

        return $query->fetchAll(PDO::FETCH_ASSOC);
    }

}
