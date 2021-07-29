<?php

namespace Firm\Infrastructure\Persistence\Doctrine\Repository;

use Doctrine\ORM\EntityRepository;
use Firm\Application\Service\Firm\Program\ActivityType\MeetingRepository as MeetingRepository2;
use Firm\Domain\Model\Firm\Program\ActivityType\Meeting;
use Firm\Domain\Task\Shared\Firm\Program\ActivityType\MeetingRepository;
use Resources\Uuid;

class DoctrineMeetingRepository extends EntityRepository implements MeetingRepository, MeetingRepository2
{

    public function add(Meeting $meeting): void
    {
        $em = $this->getEntityManager();
        $em->persist($meeting);
        $em->flush();
    }

    public function nextIdentity(): string
    {
        return Uuid::generateUuid4();
    }

}
