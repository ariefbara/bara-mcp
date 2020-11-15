<?php

namespace Query\Infrastructure\Persistence\Doctrine\Repository;

use Doctrine\ORM\{
    EntityRepository,
    NoResultException
};
use Query\{
    Application\Service\Firm\Program\Activity\InviteeRepository,
    Domain\Model\Firm\Client\ClientParticipant,
    Domain\Model\Firm\Manager\ManagerActivity,
    Domain\Model\Firm\Program\Activity\Invitee,
    Domain\Model\Firm\Program\Consultant\ConsultantActivity,
    Domain\Model\Firm\Program\Coordinator\CoordinatorActivity,
    Domain\Model\Firm\Program\Participant\ParticipantActivity,
    Domain\Model\Firm\Team\TeamProgramParticipation,
    Domain\Model\User\UserParticipant
};
use Resources\{
    Exception\RegularException,
    Infrastructure\Persistence\Doctrine\PaginatorBuilder
};

class DoctrineInviteeRepository extends EntityRepository implements InviteeRepository
{

    public function allInviteesInManagerActivity(
            string $firmId, string $managerId, string $activityId, int $page, int $pageSize)
    {
        $params = [
            "firmId" => $firmId,
            "managerId" => $managerId,
            "activityId" => $activityId,
        ];

        $activityQb = $this->getEntityManager()->createQueryBuilder();
        $activityQb->select("t_activity.id")
                ->from(ManagerActivity::class, "managerActivity")
                ->leftJoin("managerActivity.activity", "t_activity")
                ->andWhere($activityQb->expr()->eq("t_activity.id", ":activityId"))
                ->leftJoin("managerActivity.manager", "manager")
                ->andWhere($activityQb->expr()->eq("manager.id", ":managerId"))
                ->leftJoin("manager.firm", "firm")
                ->andWhere($activityQb->expr()->eq("firm.id", ":firmId"))
                ->setMaxResults(1);

        $qb = $this->createQueryBuilder("invitee");
        $qb->select("invitee")
                ->leftJoin("invitee.activity", "activity")
                ->andWhere($qb->expr()->in("activity.id", $activityQb->getDQL()))
                ->setParameters($params);

        return PaginatorBuilder::build($qb->getQuery(), $page, $pageSize);
    }

    public function anInviteeInManagerActivity(string $firmId, string $managerId, string $inviteeId): Invitee
    {
        $params = [
            "firmId" => $firmId,
            "managerId" => $managerId,
            "inviteeId" => $inviteeId,
        ];

        $activityQb = $this->getEntityManager()->createQueryBuilder();
        $activityQb->select("t_activity.id")
                ->from(ManagerActivity::class, "managerActivity")
                ->leftJoin("managerActivity.activity", "t_activity")
                ->leftJoin("managerActivity.manager", "manager")
                ->andWhere($activityQb->expr()->eq("manager.id", ":managerId"))
                ->leftJoin("manager.firm", "firm")
                ->andWhere($activityQb->expr()->eq("firm.id", ":firmId"));

        $qb = $this->createQueryBuilder("invitee");
        $qb->select("invitee")
                ->andWhere($qb->expr()->eq("invitee.id", ":inviteeId"))
                ->leftJoin("invitee.activity", "activity")
                ->andWhere($qb->expr()->in("activity.id", $activityQb->getDQL()))
                ->setParameters($params)
                ->setMaxResults(1);

        try {
            return $qb->getQuery()->getSingleResult();
        } catch (NoResultException $ex) {
            $errorDetail = "not found: invitee not found";
            throw RegularException::notFound($errorDetail);
        }
    }

    public function allInvitationsInCoordinatorActivity(string $firmId, string $personnelId, string $activityId,
            int $page, int $pageSize)
    {
        $params = [
            "firmId" => $firmId,
            "personnelId" => $personnelId,
            "activityId" => $activityId,
        ];

        $activityQb = $this->getEntityManager()->createQueryBuilder();
        $activityQb->select("t_activity.id")
                ->from(CoordinatorActivity::class, "coordinatorActivity")
                ->leftJoin("coordinatorActivity.activity", "t_activity")
                ->andWhere($activityQb->expr()->eq("t_activity.id", ":activityId"))
                ->leftJoin("coordinatorActivity.coordinator", "coordinator")
                ->leftJoin("coordinator.personnel", "personnel")
                ->andWhere($activityQb->expr()->eq("personnel.id", ":personnelId"))
                ->leftJoin("personnel.firm", "firm")
                ->andWhere($activityQb->expr()->eq("firm.id", ":firmId"))
                ->setMaxResults(1);

        $qb = $this->createQueryBuilder("invitation");
        $qb->select("invitation")
                ->leftJoin("invitation.activity", "activity")
                ->andWhere($qb->expr()->in("activity.id", $activityQb->getDQL()))
                ->setParameters($params);

        return PaginatorBuilder::build($qb->getQuery(), $page, $pageSize);
    }

    public function anInvitationFromCoordinator(string $firmId, string $personnelId, string $invitationId): Invitee
    {
        $params = [
            "firmId" => $firmId,
            "personnelId" => $personnelId,
            "invitationId" => $invitationId,
        ];

        $activityQb = $this->getEntityManager()->createQueryBuilder();
        $activityQb->select("t_activity.id")
                ->from(CoordinatorActivity::class, "coordinatorActivity")
                ->leftJoin("coordinatorActivity.activity", "t_activity")
                ->leftJoin("coordinatorActivity.coordinator", "coordinator")
                ->leftJoin("coordinator.personnel", "personnel")
                ->andWhere($activityQb->expr()->eq("personnel.id", ":personnelId"))
                ->leftJoin("personnel.firm", "firm")
                ->andWhere($activityQb->expr()->eq("firm.id", ":firmId"));

        $qb = $this->createQueryBuilder("invitation");
        $qb->select("invitation")
                ->andWhere($qb->expr()->eq("invitation.id", ":invitationId"))
                ->leftJoin("invitation.activity", "activity")
                ->andWhere($qb->expr()->in("activity.id", $activityQb->getDQL()))
                ->setParameters($params)
                ->setMaxResults(1);

        try {
            return $qb->getQuery()->getSingleResult();
        } catch (NoResultException $ex) {
            $errorDetail = "not found: invitation not found";
            throw RegularException::notFound($errorDetail);
        }
    }

    public function allInvitationsInConsultantActivity(string $firmId, string $personnelId, string $activityId,
            int $page, int $pageSize)
    {
        $params = [
            "firmId" => $firmId,
            "personnelId" => $personnelId,
            "activityId" => $activityId,
        ];

        $activityQb = $this->getEntityManager()->createQueryBuilder();
        $activityQb->select("t_activity.id")
                ->from(ConsultantActivity::class, "consultantActivity")
                ->leftJoin("consultantActivity.activity", "t_activity")
                ->andWhere($activityQb->expr()->eq("t_activity.id", ":activityId"))
                ->leftJoin("consultantActivity.consultant", "consultant")
                ->leftJoin("consultant.personnel", "personnel")
                ->andWhere($activityQb->expr()->eq("personnel.id", ":personnelId"))
                ->leftJoin("personnel.firm", "firm")
                ->andWhere($activityQb->expr()->eq("firm.id", ":firmId"))
                ->setMaxResults(1);

        $qb = $this->createQueryBuilder("invitation");
        $qb->select("invitation")
                ->leftJoin("invitation.activity", "activity")
                ->andWhere($qb->expr()->in("activity.id", $activityQb->getDQL()))
                ->setParameters($params);

        return PaginatorBuilder::build($qb->getQuery(), $page, $pageSize);
    }

    public function anInvitationFromConsultant(string $firmId, string $personnelId, string $invitationId): Invitee
    {
        $params = [
            "firmId" => $firmId,
            "personnelId" => $personnelId,
            "invitationId" => $invitationId,
        ];

        $activityQb = $this->getEntityManager()->createQueryBuilder();
        $activityQb->select("t_activity.id")
                ->from(ConsultantActivity::class, "consultantActivity")
                ->leftJoin("consultantActivity.activity", "t_activity")
                ->leftJoin("consultantActivity.consultant", "consultant")
                ->leftJoin("consultant.personnel", "personnel")
                ->andWhere($activityQb->expr()->eq("personnel.id", ":personnelId"))
                ->leftJoin("personnel.firm", "firm")
                ->andWhere($activityQb->expr()->eq("firm.id", ":firmId"));

        $qb = $this->createQueryBuilder("invitation");
        $qb->select("invitation")
                ->andWhere($qb->expr()->eq("invitation.id", ":invitationId"))
                ->leftJoin("invitation.activity", "activity")
                ->andWhere($qb->expr()->in("activity.id", $activityQb->getDQL()))
                ->setParameters($params)
                ->setMaxResults(1);

        try {
            return $qb->getQuery()->getSingleResult();
        } catch (NoResultException $ex) {
            $errorDetail = "not found: invitation not found";
            throw RegularException::notFound($errorDetail);
        }
    }

    public function allInvitationsInClientParticipantActivity(
            string $firmId, string $clientId, string $activityId, int $page, int $pageSize)
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

        $activityQb = $this->getEntityManager()->createQueryBuilder();
        $activityQb->select("t_activity.id")
                ->from(ParticipantActivity::class, "participantActivity")
                ->andWhere($activityQb->expr()->eq("participantActivity.id", ":activityId"))
                ->leftJoin("participantActivity.activity", "t_activity")
                ->leftJoin("participantActivity.participant", "participant")
                ->andWhere($activityQb->expr()->in("participant.id", $participantQb->getDQL()))
                ->setMaxResults(1);

        $qb = $this->createQueryBuilder("invitation");
        $qb->select("invitation")
                ->leftJoin("invitation.activity", "activity")
                ->andWhere($qb->expr()->in("activity.id", $activityQb->getDQL()))
                ->setParameters($params);

        return PaginatorBuilder::build($qb->getQuery(), $page, $pageSize);
    }

    public function anInvitationFromClient(string $firmId, string $clientId, string $invitationId): Invitee
    {
        $params = [
            "firmId" => $firmId,
            "clientId" => $clientId,
            "invitationId" => $invitationId,
        ];

        $participantQb = $this->getEntityManager()->createQueryBuilder();
        $participantQb->select("t_participant.id")
                ->from(ClientParticipant::class, "programParticipation")
                ->leftJoin("programParticipation.participant", "t_participant")
                ->leftJoin("programParticipation.client", "client")
                ->andWhere($participantQb->expr()->eq("client.id", ":clientId"))
                ->leftJoin("client.firm", "firm")
                ->andWhere($participantQb->expr()->eq("firm.id", ":firmId"));

        $activityQb = $this->getEntityManager()->createQueryBuilder();
        $activityQb->select("t_activity.id")
                ->from(ParticipantActivity::class, "participantActivity")
                ->leftJoin("participantActivity.activity", "t_activity")
                ->leftJoin("participantActivity.participant", "participant")
                ->andWhere($activityQb->expr()->in("participant.id", $participantQb->getDQL()));

        $qb = $this->createQueryBuilder("invitation");
        $qb->select("invitation")
                ->andWhere($qb->expr()->eq("invitation.id", ":invitationId"))
                ->leftJoin("invitation.activity", "activity")
                ->andWhere($qb->expr()->in("activity.id", $activityQb->getDQL()))
                ->setParameters($params)
                ->setMaxResults(1);

        try {
            return $qb->getQuery()->getSingleResult();
        } catch (NoResultException $ex) {
            $errorDetail = "not found: invitation not found";
            throw RegularException::notFound($errorDetail);
        }
    }

    public function allInvitationsInUserParticipantActivity(string $userId, string $activityId, int $page, int $pageSize)
    {
        $params = [
            "userId" => $userId,
            "activityId" => $activityId,
        ];

        $participantQb = $this->getEntityManager()->createQueryBuilder();
        $participantQb->select("t_participant.id")
                ->from(UserParticipant::class, "programParticipation")
                ->leftJoin("programParticipation.participant", "t_participant")
                ->leftJoin("programParticipation.user", "user")
                ->andWhere($participantQb->expr()->eq("user.id", ":userId"));

        $activityQb = $this->getEntityManager()->createQueryBuilder();
        $activityQb->select("t_activity.id")
                ->from(ParticipantActivity::class, "participantActivity")
                ->andWhere($activityQb->expr()->eq("participantActivity.id", ":activityId"))
                ->leftJoin("participantActivity.activity", "t_activity")
                ->leftJoin("participantActivity.participant", "participant")
                ->andWhere($activityQb->expr()->in("participant.id", $participantQb->getDQL()))
                ->setMaxResults(1);

        $qb = $this->createQueryBuilder("invitation");
        $qb->select("invitation")
                ->leftJoin("invitation.activity", "activity")
                ->andWhere($qb->expr()->in("activity.id", $activityQb->getDQL()))
                ->setParameters($params);

        return PaginatorBuilder::build($qb->getQuery(), $page, $pageSize);
    }

    public function anInvitationFromUser(string $userId, string $invitationId): Invitee
    {
        $params = [
            "userId" => $userId,
            "invitationId" => $invitationId,
        ];

        $participantQb = $this->getEntityManager()->createQueryBuilder();
        $participantQb->select("t_participant.id")
                ->from(UserParticipant::class, "programParticipation")
                ->leftJoin("programParticipation.participant", "t_participant")
                ->leftJoin("programParticipation.user", "user")
                ->andWhere($participantQb->expr()->eq("user.id", ":userId"));

        $activityQb = $this->getEntityManager()->createQueryBuilder();
        $activityQb->select("t_activity.id")
                ->from(ParticipantActivity::class, "participantActivity")
                ->leftJoin("participantActivity.activity", "t_activity")
                ->leftJoin("participantActivity.participant", "participant")
                ->andWhere($activityQb->expr()->in("participant.id", $participantQb->getDQL()));

        $qb = $this->createQueryBuilder("invitation");
        $qb->select("invitation")
                ->andWhere($qb->expr()->eq("invitation.id", ":invitationId"))
                ->leftJoin("invitation.activity", "activity")
                ->andWhere($qb->expr()->in("activity.id", $activityQb->getDQL()))
                ->setParameters($params)
                ->setMaxResults(1);

        try {
            return $qb->getQuery()->getSingleResult();
        } catch (NoResultException $ex) {
            $errorDetail = "not found: invitation not found";
            throw RegularException::notFound($errorDetail);
        }
    }

    public function allInvitationsInTeamParticipantActivity(string $firmId, string $teamId, string $activityId,
            int $page, int $pageSize)
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

        $activityQb = $this->getEntityManager()->createQueryBuilder();
        $activityQb->select("t_activity.id")
                ->from(ParticipantActivity::class, "participantActivity")
                ->andWhere($activityQb->expr()->eq("participantActivity.id", ":activityId"))
                ->leftJoin("participantActivity.activity", "t_activity")
                ->leftJoin("participantActivity.participant", "participant")
                ->andWhere($activityQb->expr()->in("participant.id", $participantQb->getDQL()))
                ->setMaxResults(1);

        $qb = $this->createQueryBuilder("invitation");
        $qb->select("invitation")
                ->leftJoin("invitation.activity", "activity")
                ->andWhere($qb->expr()->in("activity.id", $activityQb->getDQL()))
                ->setParameters($params);

        return PaginatorBuilder::build($qb->getQuery(), $page, $pageSize);
    }

    public function anInvitationFromTeam(string $firmId, string $teamId, string $invitationId): Invitee
    {
        $params = [
            "firmId" => $firmId,
            "teamId" => $teamId,
            "invitationId" => $invitationId,
        ];

        $participantQb = $this->getEntityManager()->createQueryBuilder();
        $participantQb->select("t_participant.id")
                ->from(TeamProgramParticipation::class, "programParticipation")
                ->leftJoin("programParticipation.programParticipation", "t_participant")
                ->leftJoin("programParticipation.team", "team")
                ->andWhere($participantQb->expr()->eq("team.id", ":teamId"))
                ->leftJoin("team.firm", "firm")
                ->andWhere($participantQb->expr()->eq("firm.id", ":firmId"));

        $activityQb = $this->getEntityManager()->createQueryBuilder();
        $activityQb->select("t_activity.id")
                ->from(ParticipantActivity::class, "participantActivity")
                ->leftJoin("participantActivity.activity", "t_activity")
                ->leftJoin("participantActivity.participant", "participant")
                ->andWhere($activityQb->expr()->in("participant.id", $participantQb->getDQL()));

        $qb = $this->createQueryBuilder("invitation");
        $qb->select("invitation")
                ->andWhere($qb->expr()->eq("invitation.id", ":invitationId"))
                ->leftJoin("invitation.activity", "activity")
                ->andWhere($qb->expr()->in("activity.id", $activityQb->getDQL()))
                ->setParameters($params)
                ->setMaxResults(1);

        try {
            return $qb->getQuery()->getSingleResult();
        } catch (NoResultException $ex) {
            $errorDetail = "not found: invitation not found";
            throw RegularException::notFound($errorDetail);
        }
    }

}
