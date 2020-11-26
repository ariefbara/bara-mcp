<?php

namespace Firm\Infrastructure\Persistence\Doctrine\Repository;

use Doctrine\ORM\EntityRepository;
use Firm\{
    Application\Service\Firm\Program\MeetingType\MeetingRepository,
    Domain\Model\Firm\Program\MeetingType\Meeting
};
use Resources\Uuid;

class DoctrineMeetingRepository extends EntityRepository implements MeetingRepository
{

    public function add(Meeting $meeting)
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
