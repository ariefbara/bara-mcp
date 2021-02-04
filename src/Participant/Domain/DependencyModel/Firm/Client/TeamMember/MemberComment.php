<?php

namespace Participant\Domain\DependencyModel\Firm\Client\TeamMember;

use Participant\Domain\DependencyModel\Firm\Client\TeamMembership;
use Participant\Domain\Model\Participant\Worksheet\Comment;
use Resources\Domain\Model\EntityContainEvents;

class MemberComment extends EntityContainEvents
{
    /**
     * 
     * @var TeamMembership
     */
    protected $member;
    
    /**
     * 
     * @var Comment
     */
    protected $comment;
    
    public function __construct(TeamMembership $member, $comment)
    {
        $this->member = $member;
        $this->comment = $comment;
        $this->aggregateEventFrom($this->comment);
    }

}
