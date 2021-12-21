<?php

namespace Query\Infrastructure\Persistence\Doctrine\Repository;

use Doctrine\ORM\EntityRepository;
use Doctrine\ORM\NoResultException;
use Query\Application\Service\Firm\Program\ActivityRepository;
use Query\Domain\Model\Firm\Manager\ManagerActivity;
use Query\Domain\Model\Firm\Program\Activity;
use Query\Domain\Model\Firm\Program\Consultant\ConsultantActivity;
use Query\Domain\Model\Firm\Program\Coordinator\CoordinatorActivity;
use Query\Domain\Model\Firm\Program\Participant\ParticipantActivity;
use Query\Infrastructure\QueryFilter\ActivityFilter;
use Resources\Exception\RegularException;
use Resources\Infrastructure\Persistence\Doctrine\PaginatorBuilder;

class DoctrineActivityRepository extends EntityRepository implements ActivityRepository
{

    public function anActivityInProgram(string $firmId, string $programId, string $activityId): Activity
    {
        $params = [
            "firmId" => $firmId,
            "programId" => $programId,
            "activityId" => $activityId,
        ];

        $qb = $this->createQueryBuilder("activity");
        $qb->select("activity")
                ->andWhere($qb->expr()->eq("activity.id", ":activityId"))
                ->leftJoin("activity.activityType", "activityType")
                ->leftJoin("activityType.program", "program")
                ->andWhere($qb->expr()->eq("program.id", ":programId"))
                ->leftJoin("program.firm", "firm")
                ->andWhere($qb->expr()->eq("firm.id", ":firmId"))
                ->setParameters($params)
                ->setMaxResults(1);

        try {
            return $qb->getQuery()->getSingleResult();
        } catch (NoResultException $ex) {
            $errorDetail = "not found: activity not found";
            throw RegularException::notFound($errorDetail);
        }
    }

    public function anActivityInFirm(string $firmId, string $activityId): Activity
    {
        $params = [
            "firmId" => $firmId,
            "activityId" => $activityId,
        ];

        $qb = $this->createQueryBuilder("activity");
        $qb->select("activity")
                ->andWhere($qb->expr()->eq("activity.id", ":activityId"))
                ->leftJoin("activity.activityType", "activityType")
                ->leftJoin("activityType.program", "program")
                ->leftJoin("program.firm", "firm")
                ->andWhere($qb->expr()->eq("firm.id", ":firmId"))
                ->setParameters($params)
                ->setMaxResults(1);

        try {
            return $qb->getQuery()->getSingleResult();
        } catch (NoResultException $ex) {
            $errorDetail = "not found: activity not found";
            throw RegularException::notFound($errorDetail);
        }
    }

    public function allActivitiesInProgram(
            string $firmId, string $programId, int $page, int $pageSize, ActivityFilter $activityFilter)
    {
        $offset = $pageSize * ($page - 1);

        $parameters = [
            "firmId" => $firmId,
            "programId" => $programId,
        ];
        
        $statement = <<<_STATEMENT
SELECT
    Activity.id,
    Activity.name,
    Activity.description,
    Activity.location,
    Activity.note,
    Activity.cancelled,
    Activity.createdTime,
    Activity.startDateTime startTime,
    Activity.endDateTime endTime,
    ActivityType.id activityTypeId,
    ActivityType.name activityTypeName,
    Manager.id managerId,
    Coordinator.id coordinatorId,
    Consultant.id consultantId,
    ParticipantInvitee.Participant_id participantId,
    COALESCE(
        Manager.name,
        CONCAT(Personnel.firstName, ' ', COALESCE(Personnel.lastName, '')), 
        CONCAT(User.firstName, ' ', COALESCE(User.lastName, '')), 
        CONCAT(Client.firstName, ' ', COALESCE(Client.lastName, '')), 
        Team.name
    ) initiatorName
FROM Activity
LEFT JOIN ActivityType ON ActivityType.id = Activity.ActivityType_id
LEFT JOIN Program ON Program.id = ActivityType.Program_id
LEFT JOIN Invitee ON Invitee.Activity_id = Activity.id AND Invitee.anInitiator = true
LEFT JOIN ManagerInvitee ON ManagerInvitee.Invitee_id = Invitee.id
LEFT JOIN Manager ON Manager.id = ManagerInvitee.Manager_id
LEFT JOIN CoordinatorInvitee ON CoordinatorInvitee.Invitee_id = Invitee.id
LEFT JOIN Coordinator ON Coordinator.id = CoordinatorInvitee.Coordinator_id
LEFT JOIN ConsultantInvitee ON ConsultantInvitee.Invitee_id = Invitee.id
LEFT JOIN Consultant ON Consultant.id = ConsultantInvitee.Consultant_id
LEFT JOIN Personnel ON Personnel.id = Consultant.Personnel_id OR Personnel.id = Coordinator.Personnel_id
LEFT JOIN ParticipantInvitee ON ParticipantInvitee.Invitee_id = Invitee.id
LEFT JOIN ClientParticipant ON ClientParticipant.Participant_id = ParticipantInvitee.Participant_id
LEFT JOIN Client ON Client.id = ClientParticipant.Client_id
LEFT JOIN TeamParticipant ON TeamParticipant.Participant_id = ParticipantInvitee.Participant_id
LEFT JOIN Team ON Team.id = TeamParticipant.Team_id
LEFT JOIN UserParticipant ON UserParticipant.Participant_id = ParticipantInvitee.Participant_id
LEFT JOIN User ON User.id = UserParticipant.User_id
WHERE Program.Firm_id = :firmId
    AND Program.id = :programId
    {$activityFilter->writeFromSqlClause('Activity', $parameters)}
    {$activityFilter->writeToSqlClause('Activity', $parameters)}
    {$activityFilter->writeCancelledStatusSqlClause('Activity', $parameters)}
    {$activityFilter->writeActivityTypeSqlClause('ActivityType', $parameters)}
    {$activityFilter->writeInitiatorTypeSqlClause('ManagerInvitee.Manager_id', 'CoordinatorInvitee.Coordinator_id', 'ConsultantInvitee.Consultant_id', 'ParticipantInvitee.Participant_id', $parameters)}
{$activityFilter->writeOrderSqlClause('Activity')}
LIMIT {$offset}, {$pageSize}
_STATEMENT;
        $query = $this->getEntityManager()->getConnection()->prepare($statement);
        return [
            'total' => $this->totalCountOfAllActivitiesInProgram($firmId, $programId, $activityFilter),
            'list' => $query->executeQuery($parameters)->fetchAllAssociative(),
        ];
    }
    
    public function totalCountOfAllActivitiesInProgram(string $firmId, string $programId, ActivityFilter $activityFilter): ?int
    {
        $parameters = [
            "firmId" => $firmId,
            "programId" => $programId,
        ];
        
        $statement = <<<_STATEMENT
SELECT COUNT(Activity.id) total
FROM Activity
LEFT JOIN ActivityType ON ActivityType.id = Activity.ActivityType_id
LEFT JOIN Program ON Program.id = ActivityType.Program_id
LEFT JOIN Invitee ON Invitee.Activity_id = Activity.id AND Invitee.anInitiator = true
LEFT JOIN ManagerInvitee ON ManagerInvitee.Invitee_id = Invitee.id
LEFT JOIN CoordinatorInvitee ON CoordinatorInvitee.Invitee_id = Invitee.id
LEFT JOIN ConsultantInvitee ON ConsultantInvitee.Invitee_id = Invitee.id
LEFT JOIN ParticipantInvitee ON ParticipantInvitee.Invitee_id = Invitee.id
WHERE Program.Firm_id = :firmId
    AND Program.id = :programId
    {$activityFilter->writeFromSqlClause('Activity', $parameters)}
    {$activityFilter->writeToSqlClause('Activity', $parameters)}
    {$activityFilter->writeCancelledStatusSqlClause('Activity', $parameters)}
    {$activityFilter->writeActivityTypeSqlClause('ActivityType', $parameters)}
    {$activityFilter->writeInitiatorTypeSqlClause('ManagerInvitee.Manager_id', 'CoordinatorInvitee.Coordinator_id', 'ConsultantInvitee.Consultant_id', 'ParticipantInvitee.Participant_id', $parameters)}
_STATEMENT;
        $query = $this->getEntityManager()->getConnection()->prepare($statement);
        return $query->executeQuery($parameters)->fetchFirstColumn()[0];
    }

}
