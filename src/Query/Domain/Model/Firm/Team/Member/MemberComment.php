<?php

namespace Query\Domain\Model\Firm\Team\Member;

use Query\Domain\Model\Firm\Program\Participant\Worksheet\Comment;
use Query\Domain\Model\Firm\Team\Member;

class MemberComment
{

    /**
     * 
     * @var Member
     */
    protected $member;

    /**
     * 
     * @var Comment
     */
    protected $comment;

    public function getMember(): Member
    {
        return $this->member;
    }

    public function getComment(): Comment
    {
        return $this->comment;
    }

    protected function __construct()
    {
        
    }

}
