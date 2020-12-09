<?php

namespace Notification\Infrastructure\Persistence\Doctrine\Repository;

use Doctrine\ORM\EntityRepository;
use Notification\Application\Service\MeetingRepository;
use Notification\Domain\Model\Firm\Program\MeetingType\Meeting;
use Resources\Exception\RegularException;

class DoctrineMeetingRepository extends EntityRepository implements MeetingRepository
{

    public function ofId(string $meetingId): Meeting
    {
        $meeting = $this->findOneBy(["id" => $meetingId]);
        if (empty($meeting)) {
            $errorDetail = "not found: meeting not found";
            throw RegularException::notFound($errorDetail);
        }
        return $meeting;
    }

    public function update(): void
    {
        $this->getEntityManager()->flush();
    }

}
