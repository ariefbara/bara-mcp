<?php

namespace Query\Infrastructure\Persistence\Doctrine\Repository;

use Doctrine\ORM\EntityRepository;
use Doctrine\ORM\NoResultException;
use Doctrine\ORM\QueryBuilder;
use Query\Application\Auth\MeetingAttendeeRepository as InterfaceForAuthorization;
use Query\Application\Service\Firm\Program\Activity\InviteeRepository;
use Query\Application\Service\Personnel\InviteeRepository as InterfaceForPersonnel;
use Query\Domain\Model\Firm\Client\ClientParticipant;
use Query\Domain\Model\Firm\Manager\ManagerActivity;
use Query\Domain\Model\Firm\Manager\ManagerInvitee;
use Query\Domain\Model\Firm\Program\Activity\Invitee;
use Query\Domain\Model\Firm\Program\Consultant\ConsultantActivity;
use Query\Domain\Model\Firm\Program\Consultant\ConsultantInvitee;
use Query\Domain\Model\Firm\Program\Coordinator\CoordinatorActivity;
use Query\Domain\Model\Firm\Program\Coordinator\CoordinatorInvitee;
use Query\Domain\Model\Firm\Program\Participant\ParticipantActivity;
use Query\Domain\Model\Firm\Program\Participant\ParticipantInvitee;
use Query\Domain\Model\Firm\Team\Member;
use Query\Domain\Model\Firm\Team\TeamProgramParticipation;
use Query\Domain\Model\User\UserParticipant;
use Query\Infrastructure\QueryFilter\InviteeFilter;
use Resources\Exception\RegularException;
use Resources\Infrastructure\Persistence\Doctrine\PaginatorBuilder;

class DoctrineInviteeRepository extends EntityRepository implements InviteeRepository, InterfaceForAuthorization, InterfaceForPersonnel
{

    protected function applyFilter(QueryBuilder $qb, ?InviteeFilter $inviteeFilter): void
    {
        if (!isset($inviteeFilter)) {
            return;
        }
        $from = $inviteeFilter->getFrom();
        $to = $inviteeFilter->getTo();
        if (isset($from) || isset($to)) {
            $qb->leftJoin('invitee.activity', 'activity');
            if (isset($from)) {
                $qb->andWhere($qb->expr()->gte('activity.startEndTime.startDateTime', ':from'))
                        ->setParameter('from', $from);
            }
            if (isset($to)) {
                $qb->andWhere($qb->expr()->lte('activity.startEndTime.startDateTime', ':to'))
                        ->setParameter('to', $to);
            }
        }
        if (!empty($inviteeFilter->getCancelledStatus())) {
            $qb->andWhere($qb->expr()->eq('invitee.cancelled', ':cancelled'))
                    ->setParameter('cancelled', $inviteeFilter->getCancelledStatus());
        }
        if (!empty($inviteeFilter->getWillAttendStatuses())) {
            $orX = $qb->expr()->orX();
            foreach ($inviteeFilter->getWillAttendStatuses() as $willAttendStatus) {
                if (is_null($willAttendStatus)) {
                    $orX->add($qb->expr()->isNull('invitee.willAttend'));
                } else {
                    $orX->add($qb->expr()->eq('invitee.willAttend', ':willAttendStatus'));
                    $qb->setParameter('willAttendStatus', $willAttendStatus);
                }
            }
            $qb->andWhere($orX);
        }
    }

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

    public function containRecordOfActiveMeetingAttendeeCorrespondWithPersonnelWithInitiatorRole(
            string $firmId, string $personnelId, string $meetingId): bool
    {
        $params = [
            "firmId" => $firmId,
            "personnelId" => $personnelId,
            "meetingId" => $meetingId,
        ];

        $coordinatorInviteeQb = $this->getEntityManager()->createQueryBuilder();
        $coordinatorInviteeQb->select("a_invitee.id")
                ->from(CoordinatorInvitee::class, "coordinatorInvitee")
                ->leftJoin("coordinatorInvitee.invitee", "a_invitee")
                ->leftJoin("coordinatorInvitee.coordinator", "coordinator")
                ->leftJoin("coordinator.personnel", "personnel")
                ->andWhere($coordinatorInviteeQb->expr()->eq("personnel.id", ":personnelId"))
                ->leftJoin("personnel.firm", "firm")
                ->andWhere($coordinatorInviteeQb->expr()->eq("firm.id", ":firmId"));

        $consultantInviteeQb = $this->getEntityManager()->createQueryBuilder();
        $consultantInviteeQb->select("b_invitee.id")
                ->from(ConsultantInvitee::class, "consultantInvitee")
                ->leftJoin("consultantInvitee.invitee", "b_invitee")
                ->leftJoin("consultantInvitee.consultant", "consultant")
                ->leftJoin("consultant.personnel", "b_personnel")
                ->andWhere($consultantInviteeQb->expr()->eq("b_personnel.id", ":personnelId"))
                ->leftJoin("b_personnel.firm", "b_firm")
                ->andWhere($consultantInviteeQb->expr()->eq("b_firm.id", ":firmId"));

        $qb = $this->createQueryBuilder("invitee");
        $qb->select("1")
                ->andWhere($qb->expr()->eq("invitee.anInitiator", "true"))
                ->andWhere($qb->expr()->eq("invitee.cancelled", "false"))
                ->andWhere($qb->expr()->orX(
                                $qb->expr()->in("invitee.id", $coordinatorInviteeQb->getDQL()),
                                $qb->expr()->in("invitee.id", $consultantInviteeQb->getDQL())
                ))
                ->leftJoin("invitee.activity", "activity")
                ->andWhere($qb->expr()->eq("activity.id", ":meetingId"))
                ->setParameters($params)
                ->setMaxResults(1);

        return !empty($qb->getQuery()->getResult());
    }

    public function allInviteesInMeeting(string $firmId, string $meetingId, int $page, int $pageSize,
            ?bool $initiatorStatus)
    {
        $params = [
            "firmId" => $firmId,
            "meetingId" => $meetingId,
        ];

        $qb = $this->createQueryBuilder("invitee");
        $qb->select("invitee")
                ->andWhere($qb->expr()->eq("invitee.cancelled", "false"))
                ->leftJoin("invitee.activity", "activity")
                ->andWhere($qb->expr()->eq("activity.id", ":meetingId"))
                ->leftJoin("activity.activityType", "activityType")
                ->leftJoin("activityType.program", "program")
                ->leftJoin("program.firm", "firm")
                ->andWhere($qb->expr()->eq("firm.id", ":firmId"))
                ->setParameters($params);

        if (isset($initiatorStatus)) {
            $qb->andWhere($qb->expr()->eq("invitee.anInitiator", ":initiatorStatus"))
                    ->setParameter("initiatorStatus", $initiatorStatus);
        }

        return PaginatorBuilder::build($qb->getQuery(), $page, $pageSize);
    }

    public function anInviteeInMeeting(string $firmId, string $meetingId, string $inviteeId): Invitee
    {
        $params = [
            "firmId" => $firmId,
            "meetingId" => $meetingId,
            "inviteeId" => $inviteeId,
        ];

        $qb = $this->createQueryBuilder("invitee");
        $qb->select("invitee")
                ->andWhere($qb->expr()->eq("invitee.id", ":inviteeId"))
                ->leftJoin("invitee.activity", "activity")
                ->andWhere($qb->expr()->eq("activity.id", ":meetingId"))
                ->leftJoin("activity.activityType", "activityType")
                ->leftJoin("activityType.program", "program")
                ->leftJoin("program.firm", "firm")
                ->andWhere($qb->expr()->eq("firm.id", ":firmId"))
                ->setParameters($params)
                ->setMaxResults(1);

        try {
            return $qb->getQuery()->getSingleResult();
        } catch (NoResultException $ex) {
            $errorDetail = "not found: invitee not found";
            throw RegularException::notFound($errorDetail);
        }
    }

    public function containRecordOfActiveMeetingAttendeeCorrespondWithManagerWithInitiatorRole(
            string $firmId, string $managerId, string $meetingId): bool
    {
        $params = [
            "firmId" => $firmId,
            "managerId" => $managerId,
            "meetingId" => $meetingId,
        ];

        $inviteeQb = $this->getEntityManager()->createQueryBuilder();
        $inviteeQb->select("a_invitee.id")
                ->from(ManagerInvitee::class, "managerInvitee")
                ->leftJoin("managerInvitee.invitee", "a_invitee")
                ->leftJoin("managerInvitee.manager", "manager")
                ->andWhere($inviteeQb->expr()->eq("manager.id", ":managerId"))
                ->leftJoin("manager.firm", "firm")
                ->andWhere($inviteeQb->expr()->eq("firm.id", ":firmId"));

        $qb = $this->createQueryBuilder("invitee");
        $qb->select("1")
                ->andWhere($qb->expr()->eq("invitee.anInitiator", "true"))
                ->andWhere($qb->expr()->eq("invitee.cancelled", "false"))
                ->andWhere($qb->expr()->in("invitee.id", $inviteeQb->getDQL()))
                ->leftJoin("invitee.activity", "activity")
                ->andWhere($qb->expr()->eq("activity.id", ":meetingId"))
                ->setParameters($params)
                ->setMaxResults(1);

        return !empty($qb->getQuery()->getResult());
    }

    public function containRecordOfActiveMeetingAttendeeCorrespondWithClientAsProgramParticipantHavingInitiatorRole(
            string $firmId, string $clientId, string $meetingId): bool
    {
        $params = [
            "firmId" => $firmId,
            "clientId" => $clientId,
            "meetingId" => $meetingId,
        ];

        $participantQb = $this->getEntityManager()->createQueryBuilder();
        $participantQb->select("b_participant.id")
                ->from(ClientParticipant::class, "clientParticipant")
                ->leftJoin("clientParticipant.participant", "b_participant")
                ->leftJoin("clientParticipant.client", "client")
                ->andWhere($participantQb->expr()->eq("client.id", ":clientId"))
                ->leftJoin("client.firm", "firm")
                ->andWhere($participantQb->expr()->eq("firm.id", ":firmId"));

        $inviteeQb = $this->getEntityManager()->createQueryBuilder();
        $inviteeQb->select("a_invitee.id")
                ->from(ParticipantInvitee::class, "participantInvitee")
                ->leftJoin("participantInvitee.invitee", "a_invitee")
                ->leftJoin("participantInvitee.participant", "participant")
                ->andWhere($inviteeQb->expr()->in("participant.id", $participantQb->getDQL()));

        $qb = $this->createQueryBuilder("invitee");
        $qb->select("1")
                ->andWhere($qb->expr()->eq("invitee.anInitiator", "true"))
                ->andWhere($qb->expr()->eq("invitee.cancelled", "false"))
                ->andWhere($qb->expr()->in("invitee.id", $inviteeQb->getDQL()))
                ->leftJoin("invitee.activity", "activity")
                ->andWhere($qb->expr()->eq("activity.id", ":meetingId"))
                ->setParameters($params)
                ->setMaxResults(1);

        return !empty($qb->getQuery()->getResult());
    }

    public function containRecordOfActiveMeetingAttendeeCorrespondWithTeamAsProgramParticipantHavingInitiatorRole(
            string $firmId, string $teamId, string $meetingId): bool
    {
        $params = [
            "firmId" => $firmId,
            "teamId" => $teamId,
            "meetingId" => $meetingId,
        ];

        $participantQb = $this->getEntityManager()->createQueryBuilder();
        $participantQb->select("b_participant.id")
                ->from(TeamProgramParticipation::class, "teamParticipant")
                ->leftJoin("teamParticipant.programParticipation", "b_participant")
                ->leftJoin("teamParticipant.team", "team")
                ->andWhere($participantQb->expr()->eq("team.id", ":teamId"))
                ->leftJoin("team.firm", "firm")
                ->andWhere($participantQb->expr()->eq("firm.id", ":firmId"));

        $inviteeQb = $this->getEntityManager()->createQueryBuilder();
        $inviteeQb->select("a_invitee.id")
                ->from(ParticipantInvitee::class, "participantInvitee")
                ->leftJoin("participantInvitee.invitee", "a_invitee")
                ->leftJoin("participantInvitee.participant", "participant")
                ->andWhere($inviteeQb->expr()->in("participant.id", $participantQb->getDQL()));

        $qb = $this->createQueryBuilder("invitee");
        $qb->select("1")
                ->andWhere($qb->expr()->eq("invitee.anInitiator", "true"))
                ->andWhere($qb->expr()->eq("invitee.cancelled", "false"))
                ->andWhere($qb->expr()->in("invitee.id", $inviteeQb->getDQL()))
                ->leftJoin("invitee.activity", "activity")
                ->andWhere($qb->expr()->eq("activity.id", ":meetingId"))
                ->setParameters($params)
                ->setMaxResults(1);

        return !empty($qb->getQuery()->getResult());
    }

    public function containRecordOfActiveMeetingAttendeeCorrespondWithUserAsProgramParticipantHavingInitiatorRole(
            string $userId, string $meetingId): bool
    {
        $params = [
            "userId" => $userId,
            "meetingId" => $meetingId,
        ];

        $participantQb = $this->getEntityManager()->createQueryBuilder();
        $participantQb->select("b_participant.id")
                ->from(UserParticipant::class, "userParticipant")
                ->leftJoin("userParticipant.participant", "b_participant")
                ->leftJoin("userParticipant.user", "user")
                ->andWhere($participantQb->expr()->eq("user.id", ":userId"));

        $inviteeQb = $this->getEntityManager()->createQueryBuilder();
        $inviteeQb->select("a_invitee.id")
                ->from(ParticipantInvitee::class, "participantInvitee")
                ->leftJoin("participantInvitee.invitee", "a_invitee")
                ->leftJoin("participantInvitee.participant", "participant")
                ->andWhere($inviteeQb->expr()->in("participant.id", $participantQb->getDQL()));

        $qb = $this->createQueryBuilder("invitee");
        $qb->select("1")
                ->andWhere($qb->expr()->eq("invitee.anInitiator", "true"))
                ->andWhere($qb->expr()->eq("invitee.cancelled", "false"))
                ->andWhere($qb->expr()->in("invitee.id", $inviteeQb->getDQL()))
                ->leftJoin("invitee.activity", "activity")
                ->andWhere($qb->expr()->eq("activity.id", ":meetingId"))
                ->setParameters($params)
                ->setMaxResults(1);

        return !empty($qb->getQuery()->getResult());
    }

    public function allActivityInvitationsToPersonnel(string $personnelId, int $page, int $pageSize,
            ?InviteeFilter $inviteeFilter)
    {
        $params = [
            'personnelId' => $personnelId,
        ];

        $coordinatorInviteeQB = $this->getEntityManager()->createQueryBuilder();
        $coordinatorInviteeQB->select('a_invitee.id')
                ->from(CoordinatorInvitee::class, 'coordinatorInvitee')
                ->leftJoin('coordinatorInvitee.invitee', 'a_invitee')
                ->leftJoin('coordinatorInvitee.coordinator', 'coordinator')
                ->leftJoin('coordinator.personnel', 'a_personnel')
                ->andWhere($coordinatorInviteeQB->expr()->eq('a_personnel.id', ':personnelId'));

        $consultantInviteeQB = $this->getEntityManager()->createQueryBuilder();
        $consultantInviteeQB->select('b_invitee.id')
                ->from(ConsultantInvitee::class, 'consultantInvitee')
                ->leftJoin('consultantInvitee.invitee', 'b_invitee')
                ->leftJoin('consultantInvitee.consultant', 'consultant')
                ->leftJoin('consultant.personnel', 'b_personnel')
                ->andWhere($consultantInviteeQB->expr()->eq('b_personnel.id', ':personnelId'));

        $qb = $this->createQueryBuilder('invitee');
        $qb->select('invitee')
                ->andWhere($qb->expr()->orX(
                                $qb->expr()->in('invitee.id', $coordinatorInviteeQB->getDQL()),
                                $qb->expr()->in('invitee.id', $consultantInviteeQB->getDQL())
                ))
                ->setParameters($params);
        $this->applyFilter($qb, $inviteeFilter);

        return PaginatorBuilder::build($qb->getQuery(), $page, $pageSize);
    }

}
