<?php

namespace Notification\Infrastructure\Persistence\Doctrine\Repository;

use Doctrine\ORM\EntityRepository;
use Notification\Application\Service\MeetingAttendeeRepository;
use Notification\Domain\Model\Firm\Program\MeetingType\Meeting\MeetingAttendee;
use Resources\Exception\RegularException;

class DoctrineMeetingAttendeeRepository extends EntityRepository implements MeetingAttendeeRepository
{
    
    public function ofId(string $meetingAttendeeId): MeetingAttendee
    {
        $meetingAttendee = $this->findOneBy(["id" => $meetingAttendeeId]);
        if (empty($meetingAttendee)) {
            $errorDetail = "not found: meeting attendee not found";
            throw RegularException::notFound($errorDetail);
        }
        return $meetingAttendee;
    }

    public function update(): void
    {
        $this->getEntityManager()->flush();
    }

}
