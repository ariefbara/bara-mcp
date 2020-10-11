<?php

namespace Notification\Infrastructure\Persistence\Doctrine\Repository;

use Doctrine\ORM\EntityRepository;
use Notification\ {
    Application\Service\CommentRepository,
    Domain\Model\Firm\Program\Participant\Worksheet\Comment
};

class DoctrineCommentRepository extends EntityRepository implements CommentRepository
{
    
    public function ofId(string $commentId): Comment
    {
        return $this->findOneBy(["id" => $commentId]);
    }

    public function update(): void
    {
        $this->getEntityManager()->flush();
    }

}
