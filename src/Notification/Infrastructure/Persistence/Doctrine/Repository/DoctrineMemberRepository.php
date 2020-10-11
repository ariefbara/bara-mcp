<?php

namespace Notification\Infrastructure\Persistence\Doctrine\Repository;

use Doctrine\ORM\EntityRepository;
use Notification\ {
    Application\Service\MemberRepository,
    Domain\Model\Firm\Team\Member
};

class DoctrineMemberRepository extends EntityRepository implements MemberRepository
{
    
    public function ofId(string $memberId): Member
    {
        return $this->findOneBy(["id" => $memberId]);
    }

}
