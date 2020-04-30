<?php

namespace Client\Infrastructure\Persistence\Doctrine\Repository;

use Client\ {
    Application\Service\Client\ProgramParticipation\ParticipantNotificationRepository,
    Domain\Model\Client\ProgramParticipation\ParticipantNotification
};
use Doctrine\ORM\EntityRepository;
use Resources\Uuid;

class DoctrineParticipantNotificationRepository extends EntityRepository implements ParticipantNotificationRepository
{
    
    public function add(ParticipantNotification $participantNotification): void
    {
        $em = $this->getEntityManager();
        $em->persist($participantNotification);
        $em->flush();
    }

    public function nextIdentity(): string
    {
        return Uuid::generateUuid4();
    }

}
