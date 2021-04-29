<?php

namespace Firm\Infrastructure\Persistence\Doctrine\Repository;

use Firm\Application\Service\Firm\Program\Mission\MissionCommentRepository;
use Firm\Domain\Model\Firm\Program\Mission\MissionComment;
use Resources\Infrastructure\Persistence\Doctrine\Repository\DoctrineEntityRepository;

class DoctrineMissionCommentRepository extends DoctrineEntityRepository implements MissionCommentRepository
{
    
    public function add(MissionComment $missionComment): void
    {
        $this->persist($missionComment);
    }

    public function ofId(string $missionCommentId): MissionComment
    {
        return $this->findOneByIdOrDie($missionCommentId, 'mission comment');
    }

}
