<?php

namespace Client\Infrastructure\Persistence\Doctrine\Repository;

use Client\ {
    Application\Listener\ClientNotificationRepository,
    Domain\Model\Client\ClientNotification
};
use Doctrine\ORM\EntityRepository;
use Resources\Uuid;

class DoctrineClientNotificationRepository extends EntityRepository implements ClientNotificationRepository
{

    public function add(ClientNotification $clientNotification): void
    {
        $em = $this->getEntityManager();
        $em->persist($clientNotification);
        $em->flush();
    }

    public function nextIdentity(): string
    {
        return Uuid::generateUuid4();
    }

}
