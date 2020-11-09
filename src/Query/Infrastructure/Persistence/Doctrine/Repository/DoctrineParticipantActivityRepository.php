<?php

namespace Query\Infrastructure\Persistence\Doctrine\Repository;

use Doctrine\ORM\ {
    EntityRepository,
    NoResultException
};
use Query\ {
    Application\Service\Firm\Program\Participant\ParticipantActivityRepository,
    Domain\Model\Firm\Client\ClientParticipant,
    Domain\Model\Firm\Program\Participant\ParticipantActivity,
    Domain\Model\Firm\Team\TeamProgramParticipation,
    Domain\Model\User\UserParticipant
};
use Resources\ {
    Exception\RegularException,
    Infrastructure\Persistence\Doctrine\PaginatorBuilder
};

class DoctrineParticipantActivityRepository extends EntityRepository implements ParticipantActivityRepository
{

    public function allActivitiesInClientProgramParticipation(
            string $firmId, string $clientId, string $programParticipationId, int $page, int $pageSize)
    {
        $params = [
            "firmId" => $firmId,
            "clientId" => $clientId,
            "programParticipationId" => $programParticipationId,
        ];

        $participantQb = $this->getEntityManager()->createQueryBuilder();
        $participantQb->select("t_participant.id")
                ->from(ClientParticipant::class, "programParticipation")
                ->andWhere($participantQb->expr()->eq("programParticipation.id", ":programParticipationId"))
                ->leftJoin("programParticipation.participant", "t_participant")
                ->leftJoin("programParticipation.client", "client")
                ->andWhere($participantQb->expr()->eq("client.id", ":clientId"))
                ->leftJoin("client.firm", "firm")
                ->andWhere($participantQb->expr()->eq("firm.id", ":firmId"))
                ->setMaxResults(1);

        $qb = $this->createQueryBuilder("participantActivity");
        $qb->select("participantActivity")
                ->leftJoin("participantActivity.participant", "participant")
                ->andWhere($qb->expr()->in("participant.id", $participantQb->getDQL()))
                ->setParameters($params);

        return PaginatorBuilder::build($qb->getQuery(), $page, $pageSize);
    }

    public function anActivityBelongsToClient(string $firmId, string $clientId, string $activityId): ParticipantActivity
    {
        $params = [
            "firmId" => $firmId,
            "clientId" => $clientId,
            "activityId" => $activityId,
        ];

        $participantQb = $this->getEntityManager()->createQueryBuilder();
        $participantQb->select("t_participant.id")
                ->from(ClientParticipant::class, "programParticipation")
                ->leftJoin("programParticipation.participant", "t_participant")
                ->leftJoin("programParticipation.client", "client")
                ->andWhere($participantQb->expr()->eq("client.id", ":clientId"))
                ->leftJoin("client.firm", "firm")
                ->andWhere($participantQb->expr()->eq("firm.id", ":firmId"));

        $qb = $this->createQueryBuilder("participantActivity");
        $qb->select("participantActivity")
                ->andWhere($qb->expr()->eq("participantActivity.id", ":activityId"))
                ->leftJoin("participantActivity.participant", "participant")
                ->andWhere($qb->expr()->in("participant.id", $participantQb->getDQL()))
                ->setParameters($params)
                ->setMaxResults(1);

        try {
            return $qb->getQuery()->getSingleResult();
        } catch (NoResultException $ex) {
            $errorDetail = "not found: activity not found";
            throw RegularException::notFound($errorDetail);
        }
    }

    public function allActivitiesInUserProgramParticipation(string $userId, string $programParticipationId, int $page,
            int $pageSize)
    {
        $params = [
            "userId" => $userId,
            "programParticipationId" => $programParticipationId,
        ];

        $participantQb = $this->getEntityManager()->createQueryBuilder();
        $participantQb->select("t_participant.id")
                ->from(UserParticipant::class, "programParticipation")
                ->andWhere($participantQb->expr()->eq("programParticipation.id", ":programParticipationId"))
                ->leftJoin("programParticipation.participant", "t_participant")
                ->leftJoin("programParticipation.user", "user")
                ->andWhere($participantQb->expr()->eq("user.id", ":userId"))
                ->setMaxResults(1);

        $qb = $this->createQueryBuilder("participantActivity");
        $qb->select("participantActivity")
                ->leftJoin("participantActivity.participant", "participant")
                ->andWhere($qb->expr()->in("participant.id", $participantQb->getDQL()))
                ->setParameters($params);

        return PaginatorBuilder::build($qb->getQuery(), $page, $pageSize);
    }

    public function anActivityBelongsToUser(string $userId, string $activityId): ParticipantActivity
    {
        $params = [
            "UserId" => $UserId,
            "activityId" => $activityId,
        ];

        $participantQb = $this->getEntityManager()->createQueryBuilder();
        $participantQb->select("t_participant.id")
                ->from(UserParticipant::class, "programParticipation")
                ->leftJoin("programParticipation.participant", "t_participant")
                ->leftJoin("programParticipation.User", "User")
                ->andWhere($participantQb->expr()->eq("User.id", ":UserId"));

        $qb = $this->createQueryBuilder("participantActivity");
        $qb->select("participantActivity")
                ->andWhere($qb->expr()->eq("participantActivity.id", ":activityId"))
                ->leftJoin("participantActivity.participant", "participant")
                ->andWhere($qb->expr()->in("participant.id", $participantQb->getDQL()))
                ->setParameters($params)
                ->setMaxResults(1);

        try {
            return $qb->getQuery()->getSingleResult();
        } catch (NoResultException $ex) {
            $errorDetail = "not found: activity not found";
            throw RegularException::notFound($errorDetail);
        }
    }

    public function allActivitiesInTeamProgramParticipation(string $firmId, string $teamId,
            string $programParticipationId, int $page, int $pageSize)
    {
        $params = [
            "firmId" => $firmId,
            "teamId" => $teamId,
            "programParticipationId" => $programParticipationId,
        ];

        $participantQb = $this->getEntityManager()->createQueryBuilder();
        $participantQb->select("t_participant.id")
                ->from(TeamProgramParticipation::class, "programParticipation")
                ->andWhere($participantQb->expr()->eq("programParticipation.id", ":programParticipationId"))
                ->leftJoin("programParticipation.programParticipation", "t_participant")
                ->leftJoin("programParticipation.team", "team")
                ->andWhere($participantQb->expr()->eq("team.id", ":teamId"))
                ->leftJoin("team.firm", "firm")
                ->andWhere($participantQb->expr()->eq("firm.id", ":firmId"))
                ->setMaxResults(1);

        $qb = $this->createQueryBuilder("participantActivity");
        $qb->select("participantActivity")
                ->leftJoin("participantActivity.participant", "participant")
                ->andWhere($qb->expr()->in("participant.id", $participantQb->getDQL()))
                ->setParameters($params);

        return PaginatorBuilder::build($qb->getQuery(), $page, $pageSize);
    }

    public function anActivityBelongsToTeam(string $firmId, string $teamId, string $activityId): ParticipantActivity
    {
        $params = [
            "firmId" => $firmId,
            "teamId" => $teamId,
            "activityId" => $activityId,
        ];

        $participantQb = $this->getEntityManager()->createQueryBuilder();
        $participantQb->select("t_participant.id")
                ->from(TeamProgramParticipation::class, "programParticipation")
                ->leftJoin("programParticipation.programParticipation", "t_participant")
                ->leftJoin("programParticipation.team", "team")
                ->andWhere($participantQb->expr()->eq("team.id", ":teamId"))
                ->leftJoin("team.firm", "firm")
                ->andWhere($participantQb->expr()->eq("firm.id", ":firmId"));

        $qb = $this->createQueryBuilder("participantActivity");
        $qb->select("participantActivity")
                ->andWhere($qb->expr()->eq("participantActivity.id", ":activityId"))
                ->leftJoin("participantActivity.participant", "participant")
                ->andWhere($qb->expr()->in("participant.id", $participantQb->getDQL()))
                ->setParameters($params)
                ->setMaxResults(1);

        try {
            return $qb->getQuery()->getSingleResult();
        } catch (NoResultException $ex) {
            $errorDetail = "not found: activity not found";
            throw RegularException::notFound($errorDetail);
        }
    }

}
