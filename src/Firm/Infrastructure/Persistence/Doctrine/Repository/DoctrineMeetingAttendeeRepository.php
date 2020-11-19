<?php

namespace Firm\Infrastructure\Persistence\Doctrine\Repository;

use Doctrine\ORM\ {
    EntityRepository,
    NoResultException
};
use Firm\ {
    Application\Service\Firm\Program\MeetingType\Meeting\AttendeeRepository,
    Domain\Model\Firm\Program\Coordinator\MeetingAttendance as CoordinatorAttendee,
    Domain\Model\Firm\Program\MeetingType\Meeting\Attendee
};
use Resources\Exception\RegularException;

class DoctrineMeetingAttendeeRepository extends EntityRepository implements AttendeeRepository
{

    public function update(): void
    {
        $this->getEntityManager()->flush();
    }

    public function aMeetingAttendanceBelongsToPersonnelCorrespondWithMeeting(
            string $firmId, string $personnelId, string $meetingId): Attendee
    {
        $params = [
            "firmId" => $firmId,
            "personnelId" => $personnelId,
            "meetingId" => $meetingId,
        ];
        
        $attendeeDql = $this->getEntityManager()->createQueryBuilder();
        $attendeeDql->select("t_attendee.id")
                ->from(CoordinatorAttendee::class, "meetingAttendance")
                ->leftJoin("meetingAttendance.attendee", "t_attendee")
                ->leftJoin("meetingAttendance.coordinator", "coordinator")
                ->leftJoin("coordinator.personnel", "personnel")
                ->andWhere($attendeeDql->expr()->eq("personnel.id", ":personnelId"))
                ->leftJoin("personnel.firm", "firm")
                ->andWhere($attendeeDql->expr()->eq("firm.id", ":firmId"));
        
        $qb = $this->createQueryBuilder("attendee");
        $qb->select("attendee")
                ->andWhere($qb->expr()->in("attendee.id", $attendeeDql->getDQL()))
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
