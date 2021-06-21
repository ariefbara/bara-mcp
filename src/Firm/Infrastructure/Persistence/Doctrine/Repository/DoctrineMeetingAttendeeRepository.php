<?php

namespace Firm\Infrastructure\Persistence\Doctrine\Repository;

use Doctrine\ORM\ {
    EntityRepository,
    NoResultException
};
use Firm\ {
    Application\Service\Firm\Program\MeetingType\Meeting\AttendeeRepository,
    Domain\Model\Firm\Program\ClientParticipant,
    Domain\Model\Firm\Program\MeetingType\Meeting\Attendee,
    Domain\Model\Firm\Program\MeetingType\Meeting\Attendee\ConsultantAttendee,
    Domain\Model\Firm\Program\MeetingType\Meeting\Attendee\CoordinatorAttendee,
    Domain\Model\Firm\Program\MeetingType\Meeting\Attendee\ManagerAttendee,
    Domain\Model\Firm\Program\MeetingType\Meeting\Attendee\ParticipantAttendee,
    Domain\Model\Firm\Program\TeamParticipant,
    Domain\Model\Firm\Program\UserParticipant,
    Domain\Service\MeetingAttendeeRepository as InterfaceForDomainService
};
use Resources\Exception\RegularException;

class DoctrineMeetingAttendeeRepository extends EntityRepository implements AttendeeRepository, InterfaceForDomainService
{

    public function update(): void
    {
        $this->getEntityManager()->flush();
    }

    public function anAttendeeBelongsToPersonnelCorrespondWithMeeting(
            string $firmId, string $personnelId, string $meetingId): Attendee
    {
        $params = [
            "firmId" => $firmId,
            "personnelId" => $personnelId,
            "meetingId" => $meetingId,
        ];

        $consultantAttendeeQb = $this->getEntityManager()->createQueryBuilder();
        $consultantAttendeeQb->select("b_attendee.id")
                ->from(ConsultantAttendee::class, "consultantAttendee")
                ->leftJoin("consultantAttendee.attendee", "b_attendee")
                ->leftJoin("consultantAttendee.consultant", "consultant")
                ->leftJoin("consultant.personnel", "b_personnel")
                ->andWhere($consultantAttendeeQb->expr()->eq("b_personnel.id", ":personnelId"))
                ->leftJoin("b_personnel.firm", "b_firm")
                ->andWhere($consultantAttendeeQb->expr()->eq("b_firm.id", ":firmId"));

        $coordinatorAttendeeQb = $this->getEntityManager()->createQueryBuilder();
        $coordinatorAttendeeQb->select("a_attendee.id")
                ->from(CoordinatorAttendee::class, "coordinatorAttendee")
                ->leftJoin("coordinatorAttendee.attendee", "a_attendee")
                ->leftJoin("coordinatorAttendee.coordinator", "coordinator")
                ->leftJoin("coordinator.personnel", "a_personnel")
                ->andWhere($coordinatorAttendeeQb->expr()->eq("a_personnel.id", ":personnelId"))
                ->leftJoin("a_personnel.firm", "a_firm")
                ->andWhere($coordinatorAttendeeQb->expr()->eq("a_firm.id", ":firmId"));

        $qb = $this->createQueryBuilder("attendee");
        $qb->select("attendee")
                ->andWhere($qb->expr()->orX(
                        $qb->expr()->in("attendee.id", $coordinatorAttendeeQb->getDQL()),
                        $qb->expr()->in("attendee.id", $consultantAttendeeQb->getDQL())
                ))
                ->leftJoin("attendee.meeting", "meeting")
                ->andWhere($qb->expr()->eq("meeting.id", ":meetingId"))
                ->setParameters($params)
                ->setMaxResults(1);

        try {
            return $qb->getQuery()->getSingleResult();
        } catch (NoResultException $ex) {
            $errorDetail = "not found: meeting attendance not found";
            throw RegularException::notFound($errorDetail);
        }
    }

    public function ofId(string $attendeeId): Attendee
    {
        $attendee = $this->findOneBy(["id" => $attendeeId]);
        if (empty($attendee)) {
            $errorDetail = "not found: attendee not found";
            throw RegularException::notFound($errorDetail);
        }
        return $attendee;
    }

    public function anAttendeeBelongsToManagerCorrespondWithMeeting(string $firmId, string $managerId, string $meetingId): Attendee
    {
        $params = [
            "firmId" => $firmId,
            "managerId" => $managerId,
            "meetingId" => $meetingId,
        ];

        $attendeeQb = $this->getEntityManager()->createQueryBuilder();
        $attendeeQb->select("t_attendee.id")
                ->from(ManagerAttendee::class, "meetingAttendance")
                ->leftJoin("meetingAttendance.attendee", "t_attendee")
                ->leftJoin("meetingAttendance.manager", "manager")
                ->andWhere($attendeeQb->expr()->eq("manager.id", ":managerId"))
                ->leftJoin("manager.firm", "firm")
                ->andWhere($attendeeQb->expr()->eq("firm.id", ":firmId"));

        $qb = $this->createQueryBuilder("attendee");
        $qb->select("attendee")
                ->andWhere($qb->expr()->in("attendee.id", $attendeeQb->getDQL()))
                ->leftJoin("attendee.meeting", "meeting")
                ->andWhere($qb->expr()->eq("meeting.id", ":meetingId"))
                ->setParameters($params)
                ->setMaxResults(1);

        try {
            return $qb->getQuery()->getSingleResult();
        } catch (NoResultException $ex) {
            $errorDetail = "not found: meeting attendance not found";
            throw RegularException::notFound($errorDetail);
        }
    }

    public function anAttendeeBelongsToClientParticipantCorrespondWithMeeting(
            string $firmId, string $clientId, string $meetingId): Attendee
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
                ->andWhere($participantQb->expr()->eq("client.id", ":clientId"));
        
        $attendeeQb = $this->getEntityManager()->createQueryBuilder();
        $attendeeQb->select("a_attendee.id")
                ->from(ParticipantAttendee::class, "participantAttendee")
                ->leftJoin("participantAttendee.attendee", "a_attendee")
                ->andWhere($attendeeQb->expr()->in("participant.id", $participantQb->getDQL()))
                ->leftJoin("participantAttendee.participant", "participant")
                ->leftJoin("participant.program", "program")
                ->leftJoin("program.firm", "firm")
                ->andWhere($attendeeQb->expr()->eq("firm.id", ":firmId"));

        $qb = $this->createQueryBuilder("attendee");
        $qb->select("attendee")
                ->andWhere($qb->expr()->in("attendee.id", $attendeeQb->getDQL()))
                ->leftJoin("attendee.meeting", "meeting")
                ->andWhere($qb->expr()->eq("meeting.id", ":meetingId"))
                ->setParameters($params)
                ->setMaxResults(1);

        try {
            return $qb->getQuery()->getSingleResult();
        } catch (NoResultException $ex) {
            $errorDetail = "not found: meeting attendance not found";
            throw RegularException::notFound($errorDetail);
        }
    }

    public function anAttendeeBelongsToUserParticipantCorrespondWithMeeting(string $userId, string $meetingId): Attendee
    {
        $params = [
            "userId" => $userId,
            "meetingId" => $meetingId,
        ];

        $participantQb = $this->getEntityManager()->createQueryBuilder();
        $participantQb->select("b_participant.id")
                ->from(UserParticipant::class, "userParticipant")
                ->leftJoin("userParticipant.participant", "b_participant")
                ->leftJoin("userParticipant.user", "b_user")
                ->andWhere($participantQb->expr()->eq("b_user.id", ":userId"));
        
        $attendeeQb = $this->getEntityManager()->createQueryBuilder();
        $attendeeQb->select("a_attendee.id")
                ->from(ParticipantAttendee::class, "participantAttendee")
                ->leftJoin("participantAttendee.attendee", "a_attendee")
                ->leftJoin("participantAttendee.participant", "participant")
                ->andWhere($attendeeQb->expr()->in("participant.id", $participantQb->getDQL()));

        $qb = $this->createQueryBuilder("attendee");
        $qb->select("attendee")
                ->andWhere($qb->expr()->in("attendee.id", $attendeeQb->getDQL()))
                ->leftJoin("attendee.meeting", "meeting")
                ->andWhere($qb->expr()->eq("meeting.id", ":meetingId"))
                ->setParameters($params)
                ->setMaxResults(1);

        try {
            return $qb->getQuery()->getSingleResult();
        } catch (NoResultException $ex) {
            $errorDetail = "not found: meeting attendance not found";
            throw RegularException::notFound($errorDetail);
        }
    }

    public function anAttendeeBelongsToTeamCorrespondWithMeeting(string $teamId, string $meetingId): Attendee
    {
        $params = [
            "teamId" => $teamId,
            "meetingId" => $meetingId,
        ];

        $participantQb = $this->getEntityManager()->createQueryBuilder();
        $participantQb->select("b_participant.id")
                ->from(TeamParticipant::class, "teamParticipant")
                ->leftJoin("teamParticipant.participant", "b_participant")
                ->andWhere($participantQb->expr()->eq("teamParticipant.teamId", ":teamId"));
        
        $attendeeQb = $this->getEntityManager()->createQueryBuilder();
        $attendeeQb->select("a_attendee.id")
                ->from(ParticipantAttendee::class, "participantAttendee")
                ->leftJoin("participantAttendee.attendee", "a_attendee")
                ->andWhere($attendeeQb->expr()->in("participant.id", $participantQb->getDQL()))
                ->leftJoin("participantAttendee.participant", "participant");

        $qb = $this->createQueryBuilder("attendee");
        $qb->select("attendee")
                ->andWhere($qb->expr()->in("attendee.id", $attendeeQb->getDQL()))
                ->leftJoin("attendee.meeting", "meeting")
                ->andWhere($qb->expr()->eq("meeting.id", ":meetingId"))
                ->setParameters($params)
                ->setMaxResults(1);

        try {
            return $qb->getQuery()->getSingleResult();
        } catch (NoResultException $ex) {
            $errorDetail = "not found: meeting attendance not found";
            throw RegularException::notFound($errorDetail);
        }
    }

}
