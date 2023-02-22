<?php

namespace Query\Infrastructure\Persistence\Doctrine\Repository;

use Doctrine\ORM\EntityRepository;
use Doctrine\ORM\NoResultException;
use PDO;
use Query\Application\Auth\Firm\Program\ConsultantRepository as InterfaceForAuthorization;
use Query\Application\Service\Consultant\ConsultantRepository as ConsultantRepository2;
use Query\Application\Service\ConsultantRepository as InterfaceForGuest;
use Query\Application\Service\Firm\Program\ConsultantRepository;
use Query\Application\Service\Personnel\AsProgramConsultant\ConsultantRepository as ConsultantRepository3;
use Query\Domain\Model\Firm\Program\Consultant;
use Query\Domain\Service\Firm\Program\MentorRepository;
use Query\Domain\Task\Dependency\Firm\Program\ConsultantRepository as ConsultantRepository4;
use Resources\Exception\RegularException;
use Resources\Infrastructure\Persistence\Doctrine\PaginatorBuilder;
use SharedContext\Domain\ValueObject\DeclaredMentoringStatus;

class DoctrineConsultantRepository extends EntityRepository implements ConsultantRepository, InterfaceForAuthorization, InterfaceForGuest,
        ConsultantRepository2, MentorRepository, ConsultantRepository3, ConsultantRepository4
{

    public function aProgramConsultationOfPersonnel(string $firmId, string $personnelId, string $programConsultationId): Consultant
    {
        $params = [
            'firmId' => $firmId,
            'personnelId' => $personnelId,
            'programConsultationId' => $programConsultationId,
        ];

        $qb = $this->createQueryBuilder('programConsultation');
        $qb->select('programConsultation')
                ->andWhere($qb->expr()->eq('programConsultation.id', ':programConsultationId'))
                ->leftJoin('programConsultation.personnel', 'personnel')
                ->andWhere($qb->expr()->eq('personnel.id', ':personnelId'))
                ->leftJoin('personnel.firm', 'firm')
                ->andWhere($qb->expr()->eq('firm.id', ':firmId'))
                ->setParameters($params)
                ->setMaxResults(1);

        try {
            return $qb->getQuery()->getSingleResult();
        } catch (NoResultException $ex) {
            $errorDetail = 'not found: program consultation not found';
            throw RegularException::notFound($errorDetail);
        }
    }

    public function all(string $firmId, string $programId, int $page, int $pageSize)
    {
        $params = [
            'firmId' => $firmId,
            'programId' => $programId,
        ];

        $qb = $this->createQueryBuilder('consultant');
        $qb->select('consultant')
                ->andWhere($qb->expr()->eq('consultant.active', 'true'))
                ->leftJoin('consultant.program', 'program')
                ->andWhere($qb->expr()->eq('program.id', ':programId'))
                ->leftJoin('program.firm', 'firm')
                ->andWhere($qb->expr()->eq('firm.id', ':firmId'))
                ->setParameters($params);

        return PaginatorBuilder::build($qb->getQuery(), $page, $pageSize);
    }

    public function allProgramConsultationOfPersonnel(string $firmId, string $personnelId, int $page, int $pageSize)
    {
        $params = [
            'firmId' => $firmId,
            'personnelId' => $personnelId,
        ];

        $qb = $this->createQueryBuilder('programConsultation');
        $qb->select('programConsultation')
                ->leftJoin('programConsultation.personnel', 'personnel')
                ->andWhere($qb->expr()->eq('personnel.id', ':personnelId'))
                ->leftJoin('personnel.firm', 'firm')
                ->andWhere($qb->expr()->eq('firm.id', ':firmId'))
                ->setParameters($params);

        return PaginatorBuilder::build($qb->getQuery(), $page, $pageSize);
    }

    public function containRecordOfUnremovedConsultantCorrespondWithPersonnel(
            string $firmId, string $personnelId, string $programId): bool
    {
        $params = [
            'firmId' => $firmId,
            'programId' => $programId,
            'personnelId' => $personnelId,
        ];

        $qb = $this->createQueryBuilder('consultant');
        $qb->select('1')
                ->andWhere($qb->expr()->eq('consultant.active', 'true'))
                ->leftJoin('consultant.program', 'program')
                ->andWhere($qb->expr()->eq('program.id', ':programId'))
                ->leftJoin('program.firm', 'program_firm')
                ->andWhere($qb->expr()->eq('program_firm.id', ':firmId'))
                ->leftJoin('consultant.personnel', 'personnel')
                ->andWhere($qb->expr()->eq('personnel.id', ':personnelId'))
                ->leftJoin('personnel.firm', 'personel_firm')
                ->andWhere($qb->expr()->eq('personel_firm.id', ':firmId'))
                ->setParameters($params)
                ->setMaxResults(1);

        return !empty(count($qb->getQuery()->getResult()));
    }

    public function ofId(string $firmId, string $programId, string $consultantId): Consultant
    {
        $params = [
            'firmId' => $firmId,
            'programId' => $programId,
            'consultantId' => $consultantId,
        ];

        $qb = $this->createQueryBuilder('consultant');
        $qb->select('consultant')
                ->andWhere($qb->expr()->eq('consultant.id', ':consultantId'))
                ->leftJoin('consultant.program', 'program')
                ->andWhere($qb->expr()->eq('program.id', ':programId'))
                ->leftJoin('program.firm', 'firm')
                ->andWhere($qb->expr()->eq('firm.id', ':firmId'))
                ->setParameters($params)
                ->setMaxResults(1);

        try {
            return $qb->getQuery()->getSingleResult();
        } catch (NoResultException $ex) {
            $errorDetail = 'not found: consultant not found';
            throw RegularException::notFound($errorDetail);
        }
    }

    public function allActiveConsultantInProgram(string $programId, int $page, int $pageSize)
    {
        $params = [
            'programId' => $programId,
        ];

        $qb = $this->createQueryBuilder('consultant');
        $qb->select('consultant')
                ->andWhere($qb->expr()->eq('consultant.active', 'true'))
                ->leftJoin('consultant.program', 'program')
                ->andWhere($qb->expr()->eq('program.id', ':programId'))
                ->setParameters($params);

        return PaginatorBuilder::build($qb->getQuery(), $page, $pageSize);
    }

    public function anActiveConsultant(string $id): Consultant
    {
        $consultant = $this->findOneBy([
            'id' => $id,
            'active' => true,
        ]);
        if (empty($consultant)) {
            throw RegularException::notFound('not found: consultant not found');
        }
        return $consultant;
    }

    public function aConsultantBelongsToPersonnel(string $firmId, string $personnelId, string $consultantId): Consultant
    {
        $params = [
            'firmId' => $firmId,
            'personnelId' => $personnelId,
            'consultantId' => $consultantId,
        ];

        $qb = $this->createQueryBuilder('consultant');
        $qb->select('consultant')
                ->andWhere($qb->expr()->eq('consultant.id', ':consultantId'))
                ->leftJoin('consultant.personnel', 'personnel')
                ->andWhere($qb->expr()->eq('personnel.id', ':personnelId'))
                ->leftJoin('personnel.firm', 'firm')
                ->andWhere($qb->expr()->eq('firm.id', ':firmId'))
                ->setParameters($params)
                ->setMaxResults(1);

        try {
            return $qb->getQuery()->getSingleResult();
        } catch (NoResultException $ex) {
            throw RegularException::notFound('not found: consultant not found');
        }
    }

    public function allMentorsAccessibleToParticipant(string $participantId, int $page, int $pageSize)
    {
        $em = $this->getEntityManager();
        $params = [
            "participantId" => $participantId,
        ];

        $totalStatement = <<<_TOTAL
SELECT COUNT(Consultant.id) total
FROM Consultant
LEFT JOIN (
    SELECT Participant.Program_id programId
    FROM Participant
    WHERE Participant.id = :participantId
) _a ON _a.programId = Consultant.Program_id
WHERE Consultant.active = true AND _a.programId IS NOT NULL
_TOTAL;
        $totalQuery = $em->getConnection()->prepare($totalStatement);
        $totalQuery->execute($params);
        $total = $totalQuery->fetchAll(PDO::FETCH_ASSOC)[0]['total'];

        $offset = $pageSize * ($page - 1);
        $statement = <<<_STATEMENT
SELECT
    Consultant.id,
    Personnel.id personnelId,
    Personnel.firstName,
    Personnel.lastName,
    IF (_a.id IS NULL, false, true) isDedicatedMentor
FROM Consultant
LEFT JOIN (
    SELECT id, Consultant_id
    FROM DedicatedMentor
    WHERE cancelled = false
)_a ON Consultant_id = Consultant.id
LEFT JOIN Participant ON Participant.Program_id = Consultant.Program_id
LEFT JOIN Personnel ON Personnel.id = Consultant.Personnel_id
WHERE Participant.id = :participantId 
    AND Participant.Program_id IS NOT NULL
    AND  Consultant.active = true
GROUP BY Consultant.id
ORDER BY isDedicatedMentor DESC
LIMIT {$offset}, {$pageSize}
_STATEMENT;

        $offset = $pageSize * ($page - 1);
        $query = $em->getConnection()->prepare($statement);
        $query->execute($params);
        return [
            'total' => $total,
            'list' => $query->fetchAll(PDO::FETCH_ASSOC),
        ];
    }

    public function aMentorInProgram(string $programId, string $mentorId): Consultant
    {
        $params = [
            'programId' => $programId,
            'mentorId' => $mentorId,
        ];

        $qb = $this->createQueryBuilder('mentor');
        $qb->select('mentor')
                ->andWhere($qb->expr()->eq('mentor.id', ':mentorId'))
                ->leftJoin('mentor.program', 'program')
                ->andWhere($qb->expr()->eq('program.id', ':programId'))
                ->setParameters($params)
                ->setMaxResults(1);

        try {
            return $qb->getQuery()->getSingleResult();
        } catch (NoResultException $ex) {
            throw RegularException::notFound('not found: consultant not found');
        }
    }

    public function aConsultantCorrepondWithProgram(string $firmId, string $personnelId, string $programId): Consultant
    {
        $params = [
            'firmId' => $firmId,
            'personnelId' => $personnelId,
            'programId' => $programId,
        ];

        $qb = $this->createQueryBuilder('mentor');
        $qb->select('mentor')
                ->leftJoin('mentor.program', 'program')
                ->andWhere($qb->expr()->eq('program.id', ':programId'))
                ->leftJoin('mentor.personnel', 'personnel')
                ->andWhere($qb->expr()->eq('personnel.id', ':personnelId'))
                ->leftJoin('personnel.firm', 'firm')
                ->andWhere($qb->expr()->eq('firm.id', ':firmId'))
                ->setParameters($params)
                ->setMaxResults(1);

        try {
            return $qb->getQuery()->getSingleResult();
        } catch (NoResultException $ex) {
            throw RegularException::notFound('not found: consultant not found');
        }
    }

    public function consultantSummaryListInProgram(string $programId): array
    {
        $activeDeclaredStatus = implode(' ,', [
            DeclaredMentoringStatus::APPROVED_BY_MENTOR,
            DeclaredMentoringStatus::APPROVED_BY_PARTICIPANT,
            DeclaredMentoringStatus::DECLARED_BY_MENTOR,
            DeclaredMentoringStatus::DECLARED_BY_PARTICIPANT,
        ]);
        $parameters = [
            'programId' => $programId,
        ];
        
        $sql = <<<_SQL
SELECT
    Consultant.id,
    CONCAT(Personnel.firstName, ' ', COALESCE(Personnel.lastName, '')) name,
    _a.averageMentorRating,
    _a.submittedReportCount,
    _a.completedSessionCount,
    _a.completedMenteeCount,
    _b.worksheetCommentCount,
    _c.missionDiscussionCount,
    (_d.upcomingNegotiatedMentoringCount + _e.upcomingMentoringSlotCount) upcomingSessionCount,
    (_d.upcomingNegotiatedMentoringCount + _e.upcomingMentoringSlotParticipantCount) upcomingMenteeCount,
    _e.availableMentoringSlotSessionCount,
    _e.availableSlotCount
FROM Consultant
    INNER JOIN Personnel ON Personnel.id = Consultant.Personnel_id
    LEFT JOIN (
        SELECT
            AVG(ParticipantReport.mentorRating) averageMentorRating,
            COUNT(MentorReport.id) submittedReportCount,
            COUNT(Mentoring.id) completedMenteeCount,
            (COUNT(NegotiatedMentoring.id) + COUNT(DeclaredMentoring.id) + COUNT(DISTINCT MentoringSlot.id)) completedSessionCount,
            COALESCE(MentoringRequest.Consultant_id, DeclaredMentoring.Consultant_id, MentoringSlot.Mentor_id) consultantId
        FROM Mentoring
            LEFT JOIN NegotiatedMentoring ON NegotiatedMentoring.Mentoring_id = Mentoring.id
            LEFT JOIN MentoringRequest 
                ON MentoringRequest.id = NegotiatedMentoring.MentoringRequest_id
                AND MentoringRequest.endTime < NOW()
            LEFT JOIN DeclaredMentoring ON DeclaredMentoring.Mentoring_id = Mentoring.id
                AND DeclaredMentoring.endTime < NOW()
                AND DeclaredMentoring.declaredStatus IN ({$activeDeclaredStatus})
            LEFT JOIN BookedMentoringSlot 
                ON BookedMentoringSlot.Mentoring_id = Mentoring.id
                AND BookedMentoringSlot.cancelled = false
            LEFT JOIN MentoringSlot 
                ON MentoringSlot.id = BookedMentoringSlot.MentoringSlot_id
                AND MentoringSlot.endTime < NOW()
                AND MentoringSlot.cancelled = false
            LEFT JOIN ParticipantReport ON ParticipantReport.Mentoring_id = Mentoring.id
            LEFT JOIN MentorReport ON MentorReport.Mentoring_id = Mentoring.id
        GROUP BY consultantId
    )_a ON _a.consultantId = Consultant.id
    LEFT JOIN (
        SELECT COUNT(ConsultantComment.id) worksheetCommentCount,
            ConsultantComment.Consultant_id consultantId
        FROM ConsultantComment
            INNER JOIN Comment ON Comment.id = ConsultantComment.Comment_id AND Comment.removed = false
        GROUP BY consultantId
    )_b ON _b.consultantId = Consultant.id
    LEFT JOIN (
        SELECT COUNT(MissionComment.id) missionDiscussionCount,
            JSON_UNQUOTE(JSON_EXTRACT(MissionComment.rolePaths ,'$.mentor')) consultantId
            -- MissionComment.rolePaths->>'$.mentor' consultantId
        FROM MissionComment
        GROUP BY consultantId
    )_c ON _c.consultantId = Consultant.id
    LEFT JOIN (
        SELECT MentoringRequest.Consultant_id consultantId,
            COUNT(NegotiatedMentoring.id) upcomingNegotiatedMentoringCount
        FROM NegotiatedMentoring
            INNER JOIN MentoringRequest 
                ON MentoringRequest.id = NegotiatedMentoring.MentoringRequest_id
                AND MentoringRequest.startTime > NOW()
        GROUP BY consultantId
    )_d on _d.consultantId = Consultant.id
    LEFT JOIN (
        SELECT
            MentoringSlot.Mentor_id consultantId,
            SUM(IF(_e1.bookCount IS NOT NULL, 1, 0)) upcomingMentoringSlotCount,
            SUM(_e1.bookCount) upcomingMentoringSlotParticipantCount,
            SUM(IF(MentoringSlot.capacity > COALESCE(_e1.bookCount, 0), 1, 0)) availableMentoringSlotSessionCount,
            SUM(MentoringSlot.capacity) - SUM(_e1.bookCount) availableSlotCount
        FROM MentoringSlot
            LEFT JOIN (
                SELECT 
                    BookedMentoringSlot.MentoringSlot_id mentoringSlotId,
                    COUNT(BookedMentoringSlot.id) bookCount
                FROM BookedMentoringSlot
                WHERE BookedMentoringSlot.cancelled = false
                GROUP BY mentoringSlotId
            )_e1 ON _e1.mentoringSlotId = MentoringSlot.id
        WHERE MentoringSlot.startTime > NOW()
            AND MentoringSlot.cancelled = false
        GROUP BY consultantId
    )_e ON _e.consultantId = Consultant.id
WHERE Consultant.Program_id = :programId
    AND Consultant.active = true
_SQL;
        
        $query = $this->getEntityManager()->getConnection()->prepare($sql);
        return $query->executeQuery($parameters)->fetchAllAssociative();
    }

}
