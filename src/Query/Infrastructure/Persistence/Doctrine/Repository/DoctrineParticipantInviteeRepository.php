<?php

namespace Query\Infrastructure\Persistence\Doctrine\Repository;

use Doctrine\ORM\EntityRepository;
use Doctrine\ORM\NoResultException;
use Doctrine\ORM\QueryBuilder;
use Query\Application\Service\Firm\Program\Participant\ParticipantInvitationRepository;
use Query\Domain\Model\Firm\Client\ClientParticipant;
use Query\Domain\Model\Firm\Program\Participant\ParticipantInvitee;
use Query\Domain\Model\Firm\Team\TeamProgramParticipation;
use Query\Domain\Model\User\UserParticipant;
use Query\Infrastructure\QueryFilter\TimeIntervalFilter;
use Resources\Exception\RegularException;
use Resources\Infrastructure\Persistence\Doctrine\PaginatorBuilder;

class DoctrineParticipantInviteeRepository extends EntityRepository implements ParticipantInvitationRepository
{

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

}
