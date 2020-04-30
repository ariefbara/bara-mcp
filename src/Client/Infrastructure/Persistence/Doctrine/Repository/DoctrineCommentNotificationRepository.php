<?php

namespace Client\Infrastructure\Persistence\Doctrine\Repository;

use Client\ {
    Application\Service\Client\ProgramParticipation\Worksheet\Comment\CommentNotificationRepository,
    Domain\Model\Client\ProgramParticipation\Worksheet\Comment\CommentNotification
};
use Doctrine\ORM\EntityRepository;
use Resources\Uuid;

class DoctrineCommentNotificationRepository extends EntityRepository implements CommentNotificationRepository
{

    public function add(CommentNotification $commentNotification): void
    {
        $em = $this->getEntityManager();
        $em->persist($commentNotification);
        $em->flush();
    }

    public function nextIdentity(): string
    {
        return Uuid::generateUuid4();
    }

}
