<?php

namespace Participant\Infrastructure\Persistence\Doctrine\Repository;

use Doctrine\ORM\EntityRepository;
use Participant\Application\Service\Firm\Client\TeamMembership\ProgramParticipation\Worksheet\MemberCommentRepository;
use Participant\Domain\DependencyModel\Firm\Client\TeamMember\MemberComment;
use Resources\Uuid;

class DoctrineMemberCommentRepository extends EntityRepository implements MemberCommentRepository
{
    
    public function add(MemberComment $memberComment): void
    {
        $em = $this->getEntityManager();
        $em->persist($memberComment);
        $em->flush();
    }

    public function nextIdentity(): string
    {
        return Uuid::generateUuid4();
    }

}
