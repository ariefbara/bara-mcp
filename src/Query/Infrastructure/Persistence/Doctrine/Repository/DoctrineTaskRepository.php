<?php

namespace Query\Infrastructure\Persistence\Doctrine\Repository;

use Doctrine\ORM\EntityRepository;
use Doctrine\ORM\NoResultException;
use Query\Domain\Model\Firm\Program\Participant\Task;
use Query\Domain\Task\Dependency\Firm\Program\Participant\TaskListFilter;
use Query\Domain\Task\Dependency\Firm\Program\Participant\TaskListFilterForConsultant;
use Query\Domain\Task\Dependency\Firm\Program\Participant\TaskListFilterForCoordinator;
use Query\Domain\Task\Dependency\Firm\Program\Participant\TaskRepository;
use Resources\Exception\RegularException;
use SharedContext\Domain\ValueObject\TaskReportReviewStatus;

class DoctrineTaskRepository extends EntityRepository implements TaskRepository
{

    public function taskListInAllProgramConsultedByPersonnel(string $personnelId, TaskListFilterForConsultant $filter)
    {
        $unreviewedReportStatus = TaskReportReviewStatus::UNREVIEWED;
        $approvedReportStatus = TaskReportReviewStatus::APPROVED;
        $revisionRequiredReviewStatus = TaskReportReviewStatus::REVISION_REQUIRED;
        
        $parameters = [
            'personnelId' => $personnelId,
        ];
        $statement = <<<_STATEMENT
SELECT
    Task.name,
    Task.description,
    Task.createdTime,
    Task.modifiedTime,
    Task.cancelled,
    CASE TaskReport.reviewStatus
        WHEN {$unreviewedReportStatus} THEN 'unreviewed'
        WHEN {$approvedReportStatus} THEN 'approved'
        WHEN {$revisionRequiredReviewStatus} THEN 'revision-required'
        ELSE 'no-report-submitted'
    END reviewStatus,

    ConsultantTask.id consultantTaskId,
    CoordinatorTask.id coordinatorTaskId,
    CONCAT(Personnel.firstName, ' ', COALESCE(Personnel.lastName, '')) taskGiverName,

    Participant.id participantId,
    COALESCE(
        CONCAT(User.firstName, ' ', COALESCE(User.lastName, '')), 
        CONCAT(Client.firstName, ' ', COALESCE(Client.lastName, '')), 
        Team.name
    ) participantName,

    Consultant.id consultantId,
    Program.id programId,
    Program.name programName
FROM
    Task
    LEFT JOIN ConsultantTask ON ConsultantTask.Task_id = Task.id
        LEFT JOIN Consultant AS _consultantTaskGiver ON _consultantTaskGiver.id = ConsultantTask.Consultant_id
    LEFT JOIN CoordinatorTask ON CoordinatorTask.Task_id = Task.id
        LEFT JOIN Coordinator AS _coordinatorTaskGiver ON _coordinatorTaskGiver.id = CoordinatorTask.Coordinator_id
        LEFT JOIN Personnel ON Personnel.id = _coordinatorTaskGiver.Personnel_id OR Personnel.id = _consultantTaskGiver.Personnel_id

    LEFT JOIN TaskReport ON TaskReport.Task_id = Task.id
                
    INNER JOIN Participant ON Participant.id = Task.Participant_id AND Participant.active = true
        LEFT JOIN UserParticipant ON UserParticipant.Participant_id = Participant.id
        LEFT JOIN User ON User.id= UserParticipant.User_id
        LEFT JOIN ClientParticipant ON ClientParticipant.Participant_id = Participant.id
        LEFT JOIN Client ON Client.id = ClientParticipant.Client_id
        LEFT JOIN TeamParticipant ON TeamParticipant.Participant_id = Participant.id
        LEFT JOIN Team ON Team.id = TeamParticipant.Team_id
                
    INNER JOIN Consultant 
        ON Consultant.Program_id = Participant.Program_id
        AND Consultant.active = true
        AND Consultant.Personnel_id = :personnelId
    INNER JOIN Program ON Program.id = Consultant.Program_id

    LEFT JOIN DedicatedMentor 
        ON DedicatedMentor.Consultant_id = Consultant.id 
        AND DedicatedMentor.Participant_id = Participant.id
        AND DedicatedMentor.cancelled = false

WHERE 1
    {$filter->getOptionalConditionStatement($parameters)}
{$filter->getOrderStatement()}
{$filter->getLimitStatement()}
_STATEMENT;

        $query = $this->getEntityManager()->getConnection()->prepare($statement);
        return [
            'total' => $this->totalTaskInAllProgramConsultedByPersonnel($personnelId, $filter),
            'list' => $query->executeQuery($parameters)->fetchAllAssociative(),
        ];
    }

    protected function totalTaskInAllProgramConsultedByPersonnel(string $personnelId,
            TaskListFilterForConsultant $filter)
    {
        $parameters = [
            'personnelId' => $personnelId,
        ];
        $statement = <<<_STATEMENT
SELECT
    COUNT(*) total
FROM
    Task
    LEFT JOIN ConsultantTask ON ConsultantTask.Task_id = Task.id
    LEFT JOIN Consultant AS _consultantTaskGiver ON _consultantTaskGiver.id = ConsultantTask.Consultant_id
    LEFT JOIN CoordinatorTask ON CoordinatorTask.Task_id = Task.id

    LEFT JOIN TaskReport ON TaskReport.Task_id = Task.id
    INNER JOIN Participant ON Participant.id = Task.Participant_id AND Participant.active = true
    INNER JOIN Consultant 
        ON Consultant.Program_id = Participant.Program_id
        AND Consultant.active = true
        AND Consultant.Personnel_id = :personnelId

    LEFT JOIN DedicatedMentor 
        ON DedicatedMentor.Consultant_id = Consultant.id 
        AND DedicatedMentor.Participant_id = Participant.id
        AND DedicatedMentor.cancelled = false

WHERE 1
    {$filter->getOptionalConditionStatement($parameters)}
_STATEMENT;

        $query = $this->getEntityManager()->getConnection()->prepare($statement);
        return $query->executeQuery($parameters)->fetchFirstColumn()[0];
    }

    public function allTaskForParticipant(string $participantId, TaskListFilter $taskListFilter)
    {
        $parameters = [
            'participantId' => $participantId,
        ];

        $statement = <<<_STATEMENT
SELECT 
    Task.name,
    Task.description,
    Task.createdTime,
    Task.modifiedTime,
    Task.cancelled,
                
    ConsultantTask.id consultantTaskId,
    Consultant.id consultantId,
    Consultant.Personnel_id consultantPersonnelId,
    CONCAT(_pCons.firstName, ' ', COALESCE(_pCons.lastName, '')) consultantName,
                
    CoordinatorTask.id coordinatorTaskId,
    Coordinator.id coordinatorId,
    Coordinator.Personnel_id coordinatorPersonnelId,
    CONCAT(_pCoor.firstName, ' ', COALESCE(_pCoor.lastName, '')) coordinatorName
                
FROM Task
                
INNER JOIN Participant ON Participant.id = Task.Participant_id AND Participant.active = true
LEFT JOIN TaskReport ON TaskReport.Task_id = Task.id
                
LEFT JOIN ConsultantTask ON ConsultantTask.Task_id = Task.id
LEFT JOIN Consultant ON Consultant.id = ConsultantTask.Consultant_id
LEFT JOIN Personnel _pCons ON _pCons.id = Consultant.Personnel_id
                
LEFT JOIN CoordinatorTask ON CoordinatorTask.Task_id = Task.id
LEFT JOIN Coordinator ON Coordinator.id = CoordinatorTask.Coordinator_id
LEFT JOIN Personnel _pCoor ON _pCoor.id = Coordinator.Personnel_id
WHERE Task.Participant_id = :participantId
    {$taskListFilter->getOptionalConditionStatement($parameters)}
{$taskListFilter->getOrderStatement()}
{$taskListFilter->getLimitStatement()}
_STATEMENT;

        $query = $this->getEntityManager()->getConnection()->prepare($statement);
        return [
            'total' => $this->totalCountOfAllTaskForParticipant($participantId, $taskListFilter),
            'list' => $query->executeQuery($parameters)->fetchAllAssociative(),
        ];
    }

    protected function totalCountOfAllTaskForParticipant(string $participantId, TaskListFilter $taskListFilter)
    {
        $parameters = [
            'participantId' => $participantId
        ];

        $statement = <<<_STATEMENT
SELECT COUNT(*) total
FROM Task
                
INNER JOIN Participant ON Participant.id = Task.Participant_id AND Participant.active = true
LEFT JOIN TaskReport ON TaskReport.Task_id = Task.id
                
LEFT JOIN ConsultantTask ON ConsultantTask.Task_id = Task.id
                
LEFT JOIN CoordinatorTask ON CoordinatorTask.Task_id = Task.id
                
WHERE Task.Participant_id = :participantId
    {$taskListFilter->getOptionalConditionStatement($parameters)}
_STATEMENT;

        $query = $this->getEntityManager()->getConnection()->prepare($statement);
        return $query->executeQuery($parameters)->fetchFirstColumn()[0];
    }

    public function allTaskInProgram(string $programId, TaskListFilter $taskListFilter)
    {
        $parameters = [
            'programId' => $programId,
        ];

        $statement = <<<_STATEMENT
SELECT 
    Task.name,
    Task.description,
    Task.createdTime,
    Task.modifiedTime,
    Task.cancelled,
    ConsultantTask.id consultantTaskId,
    Consultant.id consultantId,
    Consultant.Personnel_id consultantPersonnelId,
    CONCAT(_pCons.firstName, ' ', COALESCE(_pCons.lastName, '')) consultantName,
    CoordinatorTask.id coordinatorTaskId,
    Coordinator.id coordinatorId,
    Coordinator.Personnel_id coordinatorPersonnelId,
    CONCAT(_pCoor.firstName, ' ', COALESCE(_pCoor.lastName, '')) coordinatorName,
    Participant.id participantId,
    COALESCE(
        CONCAT(User.firstName, ' ', COALESCE(User.lastName, '')), 
        CONCAT(Client.firstName, ' ', COALESCE(Client.lastName, '')), 
        Team.name
    ) participantName
FROM Task
LEFT JOIN TaskReport ON TaskReport.Task_id = Task.id
                
INNER JOIN Participant ON Participant.id = Task.Participant_id AND Participant.active = true
LEFT JOIN UserParticipant ON UserParticipant.Participant_id = Participant.id
LEFT JOIN User ON User.id= UserParticipant.User_id
LEFT JOIN ClientParticipant ON ClientParticipant.Participant_id = Participant.id
LEFT JOIN Client ON Client.id = ClientParticipant.Client_id
LEFT JOIN TeamParticipant ON TeamParticipant.Participant_id = Participant.id
LEFT JOIN Team ON Team.id = TeamParticipant.Team_id
                
LEFT JOIN ConsultantTask ON ConsultantTask.Task_id = Task.id
LEFT JOIN Consultant ON Consultant.id = ConsultantTask.Consultant_id
LEFT JOIN Personnel _pCons ON _pCons.id = Consultant.Personnel_id
                
LEFT JOIN CoordinatorTask ON CoordinatorTask.Task_id = Task.id
LEFT JOIN Coordinator ON Coordinator.id = CoordinatorTask.Coordinator_id
LEFT JOIN Personnel _pCoor ON _pCoor.id = Coordinator.Personnel_id
                
WHERE Participant.Program_id = :programId
    {$taskListFilter->getCancelledStatusCondition('Task.cancelled', $parameters)}
    {$taskListFilter->getCompletedStatusCondition('TaskReport.id')}
{$taskListFilter->getOrderStatement()}
{$taskListFilter->getPaginationFilter()->getLimitStatement()}
_STATEMENT;

        $query = $this->getEntityManager()->getConnection()->prepare($statement);
        return [
            'total' => $this->totalCountOfAllTaskInProgram($programId, $taskListFilter),
            'list' => $query->executeQuery($parameters)->fetchAllAssociative(),
        ];
    }

    protected function totalCountOfAllTaskInProgram(string $programId, TaskListFilter $taskListFilter)
    {
        $parameters = [
            'programId' => $programId
        ];

        $statement = <<<_STATEMENT
SELECT COUNT(*) total
FROM Task
LEFT JOIN TaskReport ON TaskReport.Task_id = Task.id
                
INNER JOIN Participant ON Participant.id = Task.Participant_id AND Participant.active = true
                
WHERE Participant.Program_id = :programId
    {$taskListFilter->getCancelledStatusCondition('Task.cancelled', $parameters)}
    {$taskListFilter->getCompletedStatusCondition('TaskReport.id')}
_STATEMENT;

        $query = $this->getEntityManager()->getConnection()->prepare($statement);
        return $query->executeQuery($parameters)->fetchFirstColumn()[0];
    }

    public function taskListInAllProgramCoordinatedByPersonnel(string $personnelId, TaskListFilterForCoordinator $filter)
    {
        $parameters = [
            'personnelId' => $personnelId,
        ];

        $unreviewedReportStatus = TaskReportReviewStatus::UNREVIEWED;
        $approvedReportStatus = TaskReportReviewStatus::APPROVED;
        $revisionRequiredReviewStatus = TaskReportReviewStatus::REVISION_REQUIRED;
        
        $statement = <<<_STATEMENT
SELECT 
    Task.name,
    Task.description,
    Task.createdTime,
    Task.modifiedTime,
    Task.cancelled,
    CASE TaskReport.reviewStatus
        WHEN {$unreviewedReportStatus} THEN 'unreviewed'
        WHEN {$approvedReportStatus} THEN 'approved'
        WHEN {$revisionRequiredReviewStatus} THEN 'revision-required'
        ELSE 'no-report-submitted'
    END reviewStatus,

    ConsultantTask.id consultantTaskId,
    CoordinatorTask.id coordinatorTaskId,
    CONCAT(Personnel.firstName, ' ', COALESCE(Personnel.lastName, '')) taskGiverName,

    Participant.id participantId,
    COALESCE(
        CONCAT(User.firstName, ' ', COALESCE(User.lastName, '')), 
        CONCAT(Client.firstName, ' ', COALESCE(Client.lastName, '')), 
        Team.name
    ) participantName,

    Coordinator.id coordinatorId,
    Program.id programId,
    Program.name programName
FROM
    Task
    LEFT JOIN ConsultantTask ON ConsultantTask.Task_id = Task.id
        LEFT JOIN Consultant _consultantTaskGiver ON _consultantTaskGiver.id = ConsultantTask.Consultant_id
    LEFT JOIN CoordinatorTask ON CoordinatorTask.Task_id = Task.id
        LEFT JOIN Coordinator _coordinatorTaskGiver ON _coordinatorTaskGiver.id = CoordinatorTask.Coordinator_id
        LEFT JOIN Personnel
            ON Personnel.id = _consultantTaskGiver.Personnel_id
            OR Personnel.id = _coordinatorTaskGiver.Personnel_id
                
    LEFT JOIN TaskReport ON TaskReport.Task_id = Task.id
                
    INNER JOIN Participant ON Participant.id = Task.Participant_id AND Participant.active = true
        LEFT JOIN UserParticipant ON UserParticipant.Participant_id = Participant.id
        LEFT JOIN User ON User.id= UserParticipant.User_id
        LEFT JOIN ClientParticipant ON ClientParticipant.Participant_id = Participant.id
        LEFT JOIN Client ON Client.id = ClientParticipant.Client_id
        LEFT JOIN TeamParticipant ON TeamParticipant.Participant_id = Participant.id
        LEFT JOIN Team ON Team.id = TeamParticipant.Team_id
                
    INNER JOIN Coordinator
        ON Coordinator.Program_id = Participant.Program_id
        AND Coordinator.active = true
        AND Coordinator.Personnel_id = :personnelId
    INNER JOIN Program ON Program.id = Coordinator.Program_id
WHERE 1
    {$filter->getOptionalConditionStatement($parameters)}
{$filter->getOrderStatement()}
{$filter->getLimitStatement()}
_STATEMENT;

        $query = $this->getEntityManager()->getConnection()->prepare($statement);
        return [
            'total' => $this->totalTaskInAllProgramCoordinatedByPersonnel($personnelId, $filter),
            'list' => $query->executeQuery($parameters)->fetchAllAssociative(),
        ];
    }

    protected function totalTaskInAllProgramCoordinatedByPersonnel(string $personnelId,
            TaskListFilterForCoordinator $filter)
    {
        $parameters = [
            'personnelId' => $personnelId,
        ];

        $statement = <<<_STATEMENT
SELECT
    COUNT(*) total
FROM
    Task
    LEFT JOIN ConsultantTask ON ConsultantTask.Task_id = Task.id
    LEFT JOIN CoordinatorTask ON CoordinatorTask.Task_id = Task.id
        LEFT JOIN Coordinator _coordinatorTaskGiver ON _coordinatorTaskGiver.id = CoordinatorTask.Coordinator_id
                
    LEFT JOIN TaskReport ON TaskReport.Task_id = Task.id
                
    INNER JOIN Participant ON Participant.id = Task.Participant_id AND Participant.active = true
                
    INNER JOIN Coordinator 
        ON Coordinator.Program_id = Participant.Program_id
        AND Coordinator.active = true 
        AND Coordinator.Personnel_id = :personnelId
WHERE 1
    {$filter->getOptionalConditionStatement($parameters)}
_STATEMENT;

        $query = $this->getEntityManager()->getConnection()->prepare($statement);
        return $query->executeQuery($parameters)->fetchFirstColumn()[0];
    }

    public function aTaskBelongsToParticipant(string $participantId, string $id): Task
    {
        $parameters = [
            'participantId' => $participantId,
            'id' => $id,
        ];

        $qb = $this->createQueryBuilder('task');
        $qb->select('task')
                ->andWhere($qb->expr()->eq('task.id', ':id'))
                ->leftJoin('task.participant', 'participant')
                ->andWhere($qb->expr()->eq('participant.id', ':participantId'))
                ->setParameters($parameters)
                ->setMaxResults(1);

        try {
            return $qb->getQuery()->getSingleResult();
        } catch (NoResultException $ex) {
            throw RegularException::notFound('task not found');
        }
    }

}
