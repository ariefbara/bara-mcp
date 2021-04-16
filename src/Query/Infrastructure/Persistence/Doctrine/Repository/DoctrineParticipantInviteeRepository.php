<?php

namespace Query\Infrastructure\Persistence\Doctrine\Repository;

use Doctrine\ORM\EntityRepository;
use Doctrine\ORM\NoResultException;
use Doctrine\ORM\QueryBuilder;
use Query\Application\Service\Client\ParticipantInviteeRepository;
use Query\Application\Service\Firm\Program\Participant\ParticipantInvitationRepository;
use Query\Domain\Model\Firm\Client\ClientParticipant;
use Query\Domain\Model\Firm\Program\Participant\ParticipantInvitee;
use Query\Domain\Model\Firm\Team\Member;
use Query\Domain\Model\Firm\Team\TeamProgramParticipation;
use Query\Domain\Model\User\UserParticipant;
use Query\Infrastructure\QueryFilter\InviteeFilter;
use Query\Infrastructure\QueryFilter\TimeIntervalFilter;
use Resources\Exception\RegularException;
use Resources\Infrastructure\Persistence\Doctrine\PaginatorBuilder;

class DoctrineParticipantInviteeRepository extends EntityRepository implements ParticipantInvitationRepository, ParticipantInviteeRepository
{
    
    protected function applyFilter(QueryBuilder $qb, ?InviteeFilter $inviteeFilter): void
    {
        if (!isset($inviteeFilter)) {
            return;
        }
        $from = $inviteeFilter->getFrom();
        $to = $inviteeFilter->getTo();
        if (isset($from)) {
            $qb->andWhere($qb->expr()->gte('activity.startEndTime.startDateTime', ':from'))
                    ->setParameter('from', $from);
        }
        if (isset($to)) {
            $qb->andWhere($qb->expr()->lte('activity.startEndTime.startDateTime', ':to'))
                    ->setParameter('to', $to);
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

    public function allInvitationsForClientParticipant(
            string $firmId, string $clientId, string $programParticipationId, int $page, int $pageSize,
            ?TimeIntervalFilter $timeIntervalFilter)
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

        $qb = $this->createQueryBuilder("participantInvitation");
        $qb->select("participantInvitation")
                ->leftJoin("participantInvitation.participant", "participant")
                ->andWhere($qb->expr()->in("participant.id", $participantQb->getDQL()))
                ->setParameters($params);
        
        $this->applyTimeIntervalFilter($qb, $timeIntervalFilter);
        return PaginatorBuilder::build($qb->getQuery(), $page, $pageSize);
    }

    public function anInvitationForClient(string $firmId, string $clientId, string $invitationId): ParticipantInvitee
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

        $qb = $this->createQueryBuilder("participantInvitation");
        $qb->select("participantInvitation")
                ->andWhere($qb->expr()->eq("participantInvitation.id", ":invitationId"))
                ->leftJoin("participantInvitation.participant", "participant")
                ->andWhere($qb->expr()->in("participant.id", $participantQb->getDQL()))
                ->setParameters($params)
                ->setMaxResults(1);

        try {
            return $qb->getQuery()->getSingleResult();
        } catch (NoResultException $ex) {
            $errorDetail = "not found: invitation not found";
            throw RegularException::notFound($errorDetail);
        }
    }

    public function allInvitationsForUserParticipant(
            string $userId, string $programParticipationId, int $page, int $pageSize,
            ?TimeIntervalFilter $timeIntervalFilter)
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

        $qb = $this->createQueryBuilder("participantInvitation");
        $qb->select("participantInvitation")
                ->leftJoin("participantInvitation.participant", "participant")
                ->andWhere($qb->expr()->in("participant.id", $participantQb->getDQL()))
                ->setParameters($params);

        $this->applyTimeIntervalFilter($qb, $timeIntervalFilter);
        return PaginatorBuilder::build($qb->getQuery(), $page, $pageSize);
    }

    public function anInvitationForUser(string $userId, string $invitationId): ParticipantInvitee
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

        $qb = $this->createQueryBuilder("participantInvitation");
        $qb->select("participantInvitation")
                ->andWhere($qb->expr()->eq("participantInvitation.id", ":invitationId"))
                ->leftJoin("participantInvitation.participant", "participant")
                ->andWhere($qb->expr()->in("participant.id", $participantQb->getDQL()))
                ->setParameters($params)
                ->setMaxResults(1);

        try {
            return $qb->getQuery()->getSingleResult();
        } catch (NoResultException $ex) {
            $errorDetail = "not found: invitation not found";
            throw RegularException::notFound($errorDetail);
        }
    }

    public function allInvitationsForTeamParticipant(
            string $firmId, string $teamId, string $programParticipationId, int $page, int $pageSize,
            ?TimeIntervalFilter $timeIntervalFilter)
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

        $qb = $this->createQueryBuilder("participantInvitation");
        $qb->select("participantInvitation")
                ->leftJoin("participantInvitation.participant", "participant")
                ->andWhere($qb->expr()->in("participant.id", $participantQb->getDQL()))
                ->setParameters($params);

        $this->applyTimeIntervalFilter($qb, $timeIntervalFilter);
        return PaginatorBuilder::build($qb->getQuery(), $page, $pageSize);
    }

    public function anInvitationForTeam(string $firmId, string $teamId, string $invitationId): ParticipantInvitee
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

        $qb = $this->createQueryBuilder("participantInvitation");
        $qb->select("participantInvitation")
                ->andWhere($qb->expr()->eq("participantInvitation.id", ":invitationId"))
                ->leftJoin("participantInvitation.participant", "participant")
                ->andWhere($qb->expr()->in("participant.id", $participantQb->getDQL()))
                ->setParameters($params)
                ->setMaxResults(1);

        try {
            return $qb->getQuery()->getSingleResult();
        } catch (NoResultException $ex) {
            $errorDetail = "not found: invitation not found";
            throw RegularException::notFound($errorDetail);
        }
    }
    
    protected function applyTimeIntervalFilter(QueryBuilder $qb, ?TimeIntervalFilter $timeIntervalFilter): void
    {
        if (!isset($timeIntervalFilter)) {
            return;
        }
        $qb->leftJoin("participantInvitation.invitee", "invitee")
                ->leftJoin("invitee.activity", "activity");
        if (!is_null($timeIntervalFilter->getFrom())) {
            $qb->andWhere($qb->expr()->gte("activity.startEndTime.startDateTime", ":from"))
                    ->setParameter("from", $timeIntervalFilter->getFrom());
        }
        if (!is_null($timeIntervalFilter->getTo())) {
            $qb->andWhere($qb->expr()->lte("activity.startEndTime.startDateTime", ":to"))
                    ->setParameter("to", $timeIntervalFilter->getTo());
        }
    }

    public function allAccessibleParticipantInviteeBelongsToClient(
            string $clientId, int $page, int $pageSize, ?InviteeFilter $inviteeFilter)
    {
        $params = [
            'clientId' => $clientId,
        ];
        
        $clientParticipantQB = $this->getEntityManager()->createQueryBuilder();
        $clientParticipantQB->select('a_participant.id')
                ->from(ClientParticipant::class, 'a_clientParticipant')
                ->leftJoin('a_clientParticipant.participant', 'a_participant')
                ->leftJoin('a_clientParticipant.client', 'a_client')
                ->andWhere($clientParticipantQB->expr()->eq('a_client.id', ':clientId'));
        
        $teamMemberQB = $this->getEntityManager()->createQueryBuilder();
        $teamMemberQB->select('b_team.id')
                ->from(Member::class, 'b_member')
                ->andWhere($teamMemberQB->expr()->eq('b_member.active', 'true'))
                ->leftJoin('b_member.client', 'b_client')
                ->andWhere($teamMemberQB->expr()->eq('b_client.id', ':clientId'))
                ->leftJoin('b_member.team', 'b_team');
        
        $teamParticipantQB = $this->getEntityManager()->createQueryBuilder();
        $teamParticipantQB->select('c_participant.id')
                ->from(TeamProgramParticipation::class, 'c_teamParticipant')
                ->leftJoin('c_teamParticipant.programParticipation', 'c_participant')
                ->leftJoin('c_teamParticipant.team', 'c_team')
                ->andWhere($teamParticipantQB->expr()->in('c_team.id', $teamMemberQB->getDQL()));
        
        $qb = $this->createQueryBuilder('participantInvitee');
        $qb->select('participantInvitee')
                ->leftJoin('participantInvitee.participant', 'participant')
                ->andWhere($qb->expr()->orX(
                        $qb->expr()->in('participant.id', $clientParticipantQB->getDQL()),
                        $qb->expr()->in('participant.id', $teamParticipantQB->getDQL())
                ))
                ->andWhere($qb->expr()->eq('participant.active', 'true'))
                ->leftJoin('participantInvitee.invitee', 'invitee')
                ->leftJoin('invitee.activity', 'activity')
                ->addOrderBy('activity.startEndTime.startDateTime', 'ASC')
                ->setParameters($params);
        
        $this->applyFilter($qb, $inviteeFilter);
        return PaginatorBuilder::build($qb->getQuery(), $page, $pageSize);
    }

}
