<?php

namespace Query\Infrastructure\Persistence\Doctrine\Repository;

use Doctrine\ORM\NoResultException;
use Query\Application\Service\Firm\ProgramRepository;
use Query\Application\Service\Manager\ProgramRepository as ProgramRepository2;
use Query\Application\Service\ProgramRepository as InterfaceForPublic;
use Query\Domain\Model\Firm\ParticipantTypes;
use Query\Domain\Model\Firm\Program;
use Query\Domain\Task\Dependency\Firm\ProgramRepository as ProgramRepository3;
use Query\Domain\Task\PaginationPayload;
use Resources\Exception\RegularException;
use Resources\Infrastructure\Persistence\Doctrine\PaginatorBuilder;
use Resources\Infrastructure\Persistence\Doctrine\Repository\DoctrineEntityRepository;

class DoctrineProgramRepository extends DoctrineEntityRepository implements ProgramRepository, InterfaceForPublic, ProgramRepository2, ProgramRepository3
{

    public function ofId(string $firmId, string $programId): Program
    {
        $qb = $this->createQueryBuilder('program');
        $qb->select('program')
                ->andWhere($qb->expr()->eq('program.removed', 'false'))
                ->andWhere($qb->expr()->eq('program.id', ':programId'))
                ->setParameter('programId', $programId)
                ->leftJoin('program.firm', 'firm')
                ->andWhere($qb->expr()->eq('firm.id', ':firmId'))
                ->setParameter('firmId', $firmId)
                ->setMaxResults(1);

        try {
            return $qb->getQuery()->getSingleResult();
        } catch (NoResultException $ex) {
            $errorDetail = "not found: program not found";
            throw RegularException::notFound($errorDetail);
        }
    }

    public function all(string $firmId, int $page, int $pageSize, ?string $participantType, ?bool $publishOnly = true)
    {
        $qb = $this->createQueryBuilder('program');
        $qb->select('program')
                ->andWhere($qb->expr()->eq('program.removed', 'false'))
                ->leftJoin('program.firm', 'firm')
                ->andWhere($qb->expr()->eq('firm.id', ':firmId'))
                ->setParameter('firmId', $firmId);
        
        if (isset($participantType)) {
            $qb->andWhere($qb->expr()->like("program.participantTypes.values", ":participantType"))
                    ->setParameter("participantType", "%$participantType%");
        }
        if ($publishOnly) {
            $qb->andWhere($qb->expr()->eq("program.published", "true"));
        }

        return PaginatorBuilder::build($qb->getQuery(), $page, $pageSize);
    }

    public function allProgramForUser(int $page, int $pageSize)
    {
        $qb = $this->createQueryBuilder('program');
        $qb->select('program')
                ->andWhere($qb->expr()->eq("program.published", "true"))
                ->andWhere($qb->expr()->like('program.participantTypes.values', ":participantType"))
                ->setParameter("participantType", "%".ParticipantTypes::USER_TYPE."%")
                ->andWhere($qb->expr()->eq('program.removed', 'false'));
        
        return PaginatorBuilder::build($qb->getQuery(), $page, $pageSize);
    }

    public function aPublishedProgram(string $id): Program
    {
        $program = $this->findOneBy([
            'id' => $id,
            'published' => true,
            'removed' => false,
        ]);
        
        if (empty($program)) {
            throw RegularException::notFound('not found: program not found');
        }
        return $program;
    }

    public function allPublishedProgram(int $page, int $pageSize)
    {
        $qb = $this->createQueryBuilder('program');
        $qb->select('program')
                ->andWhere($qb->expr()->eq("program.published", "true"))
                ->andWhere($qb->expr()->eq('program.removed', 'false'));
        
        return PaginatorBuilder::build($qb->getQuery(), $page, $pageSize);
    }

    public function aProgramOfId(string $id): Program
    {
        return $this->findOneByIdOrDie($id, 'program');
    }

    public function allAvailableProgramsForClient(string $clientId, PaginationPayload $paginationPayload): array
    {
        $parameters = ["clientId" => $clientId];
        $statement = <<<_STATEMENT
SELECT
    Program.id, 
    Program.name, 
    Program.price, 
    Program.autoAccept, 
    Program.programType, 
    Program.description, 
    Program.participantTypes, 
    FileInfo.folders illustrationPaths,
    FileInfo.name illustrationName
FROM RegistrationPhase
    LEFT JOIN Program ON Program.id = RegistrationPhase.Program_id
    LEFT JOIN Client ON Client.Firm_id = Program.Firm_id
    LEFT JOIN FirmFileInfo ON FirmFileInfo.id = Program.FirmFileInfo_idOfIllustration
    LEFT JOIN FileInfo ON FileInfo.id = FirmFileInfo.FileInfo_id
WHERE Client.id = :clientId
    AND Program.published = true
    AND Program.removed = false
    AND (Program.ParticipantTypes LIKE '%client%' OR Program.ParticipantTypes LIKE '%team%')
    AND ( RegistrationPhase.removed = false
        AND (
            (RegistrationPhase.startDate IS NULL AND RegistrationPhase.endDate IS NULL)
            OR (RegistrationPhase.startDate IS NULL AND RegistrationPhase.endDate >= CURDATE())
            OR (RegistrationPhase.startDate <= CURDATE() AND RegistrationPhase.endDate IS NULL)
            OR (RegistrationPhase.startDate <= CURDATE() AND RegistrationPhase.endDate >= CURDATE())
        )
    )
    AND Program.id NOT IN (
        SELECT Registrant.Program_id
        FROM ClientRegistrant
            LEFT JOIN Registrant ON Registrant.id = ClientRegistrant.Registrant_id
        WHERE ClientRegistrant.Client_id = :clientId
            AND Registrant.status IN (1, 2)
    )
    AND Program.id NOT IN (
        SELECT Participant.Program_id
        FROM ClientParticipant
            LEFT JOIN Participant ON Participant.id = ClientParticipant.Participant_id
        WHERE ClientParticipant.Client_id = :clientId
            AND Participant.active = true
    )
GROUP BY Program.id
ORDER BY Program.id ASC
LIMIT {$paginationPayload->getOffset()}, {$paginationPayload->getPageSize()}
_STATEMENT;
        $query = $this->getEntityManager()->getConnection()->prepare($statement);
        return [
            'total' => $this->totalCountOfAvailableProgramsForClient($clientId),
            'list' => $query->executeQuery($parameters)->fetchAllAssociative(),
        ];
    }
    
    protected function totalCountOfAvailableProgramsForClient(string $clientId): ?int
    {
        $parameters = ["clientId" => $clientId];
        $statement = <<<_STATEMENT
SELECT COUNT(*) Total 
FROM (
    SELECT COUNT(Program.id) total
    FROM RegistrationPhase
        LEFT JOIN Program ON Program.id = RegistrationPhase.Program_id
        LEFT JOIN Client ON Client.Firm_id = Program.Firm_id
    WHERE Client.id = :clientId
        AND Program.published = true
        AND Program.removed = false
        AND (Program.ParticipantTypes LIKE '%client%' OR Program.ParticipantTypes LIKE '%team%')
        AND ( RegistrationPhase.removed = false
            AND (
                (RegistrationPhase.startDate IS NULL AND RegistrationPhase.endDate IS NULL)
                OR (RegistrationPhase.startDate IS NULL AND RegistrationPhase.endDate >= CURDATE())
                OR (RegistrationPhase.startDate <= CURDATE() AND RegistrationPhase.endDate IS NULL)
                OR (RegistrationPhase.startDate <= CURDATE() AND RegistrationPhase.endDate >= CURDATE())
            )
        )
        AND Program.id NOT IN (
            SELECT Registrant.Program_id
            FROM ClientRegistrant
                LEFT JOIN Registrant ON Registrant.id = ClientRegistrant.Registrant_id
            WHERE ClientRegistrant.Client_id = :clientId
                AND Registrant.status IN (1, 2)
        )
        AND Program.id NOT IN (
            SELECT Participant.Program_id
            FROM ClientParticipant
                LEFT JOIN Participant ON Participant.id = ClientParticipant.Participant_id
            WHERE ClientParticipant.Client_id = :clientId
                AND Participant.active = true
        )
    GROUP BY Program.id
)_a
_STATEMENT;
        $query = $this->getEntityManager()->getConnection()->prepare($statement);
        return $query->executeQuery($parameters)->fetchFirstColumn()[0];
    }

    public function allProgramsSummaryCoordinatedByPersonnel(string $personnelId)
    {
        $statement = <<<_STATEMENT
SELECT
    Program.id,
    Program.name,
    Coordinator.id coordinatorId,
    _a.participantCount,
    _a.minCompletedMission,
    _a.maxCompletedMission,
    _a.averageCompletedMission,
    _c.missionCount,
    _b.minMetricAchievement,
    _b.maxMetricAchievement,
    _b.averageMetricAchievement,
    _b.minMetricCompletion,
    _b.maxMetricCompletion,
    _b.averageMetricCompletion
FROM Program
INNER JOIN Coordinator 
    ON Program.id = Coordinator.Program_id 
    AND Coordinator.active = true 
    AND Coordinator.Personnel_id = :personnelId
LEFT JOIN (
    SELECT 
        programId,
        COUNT(participantId) participantCount,
        MIN(completedMissionCount) minCompletedMission,
        MAX(completedMissionCount) maxCompletedMission,
        AVG(completedMissionCount) averageCompletedMission
    FROM (
        SELECT 
            Participant.id participantId, 
            Participant.Program_id programId, 
            COUNT(CompletedMission.id) completedMissionCount
        FROM Participant
        LEFT JOIN CompletedMission ON CompletedMission.Participant_id = Participant.id
        WHERE Participant.active = true
        GROUP BY Participant.id
    )_a1
    GROUP BY programId
)_a ON _a.programId = Program.id
LEFT JOIN (
    SELECT 
        programId, 
        MIN(metricAchievement) minMetricAchievement,
        MAX(metricAchievement) maxMetricAchievement,
        AVG(metricAchievement) averageMetricAchievement,
        MIN(metricCompletion) minMetricCompletion,
        MAX(metricCompletion) maxMetricCompletion,
        AVG(metricCompletion) averageMetricCompletion
    FROM (
        SELECT 
            Participant.Program_id programId, 
            MetricAssignment.id metricAssignmentId,
            SUM(
                CASE 
                    WHEN _b1a.inputValue >= AssignmentField.target 
                    THEN AssignmentField.target 
                    ELSE COALESCE(_b1a.inputValue, 0)
                END
                /AssignmentField.target
            )/COUNT(AssignmentField.id) metricAchievement,
            SUM(
                CASE WHEN _b1a.inputValue >= AssignmentField.target THEN 1 ELSE 0 END
            )/COUNT(AssignmentField.id) metricCompletion
        FROM MetricAssignment
        INNER JOIN Participant ON Participant.id = MetricAssignment.Participant_id AND Participant.active = true
        INNER JOIN AssignmentField 
            ON AssignmentField.MetricAssignment_id = MetricAssignment.id 
            and AssignmentField.disabled = false
        LEFT JOIN (
            SELECT AssignmentFieldValue.AssignmentField_id assignmentFieldId, AssignmentFieldValue.inputValue
            FROM (
                SElECT MetricAssignment_id, MAX(observationTime) observationTime
                FROM MetricAssignmentReport
                WHERE approved = true AND removed = false
                GROUP BY MetricAssignment_id
            )_b1a1
            INNER JOIN MetricAssignmentReport USING (MetricAssignment_id, observationTime)
            INNER JOIN AssignmentFieldValue 
                ON AssignmentFieldValue.MetricAssignmentReport_id = MetricAssignmentReport.id
        )_b1a ON _b1a.assignmentFieldId = AssignmentField.id
        GROUP BY metricAssignmentId
    )_b1
    GROUP BY programId
)_b ON _b.programId = Program.id
LEFT JOIN (
    SELECT Mission.Program_id programId, COUNT(Mission.id) missionCount
    FROM Mission
    WHERE Mission.published = true
    GROUP BY programId
)_c ON _c.programId = Program.id
_STATEMENT;
        $query = $this->getEntityManager()->getConnection()->prepare($statement);
        $params = [
            'personnelId' => $personnelId,
        ];
        return $query->executeQuery($params)->fetchAllAssociative();
    }

    public function listOfCoordinatedProgramByPersonnel(string $personnelId)
    {
        $parameters = [
            'personnelId' => $personnelId,
        ];
        
        $sql = <<<_SQL
SELECT
    Program.id,
    Program.name,
    Coordinator.id coordinatorId
FROM Program
    INNER JOIN Coordinator 
        ON Coordinator.Program_id = Program.id
        AND Coordinator.Personnel_id = :personnelId
        AND Coordinator.active = true
_SQL;
        
        $query = $this->getEntityManager()->getConnection()->prepare($sql);
        return $query->executeQuery($parameters)->fetchAllAssociative();
    }

}
