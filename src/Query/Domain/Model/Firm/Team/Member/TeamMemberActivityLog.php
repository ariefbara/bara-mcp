<?php

namespace Query\Domain\Model\Firm\Team\Member;

use Query\Domain\{
    Model\Firm\Team\Member,
    SharedModel\ActivityLog
};

class TeamMemberActivityLog
{

    /**
     *
     * @var Member
     */
    protected $member;

    /**
     *
     * @var string
     */
    protected $id;

    /**
     *
     * @var ActivityLog
     */
    protected $activityLog;

    public function getMember(): Member
    {
        return $this->member;
    }

    public function getId(): string
    {
        return $this->id;
    }

    protected function __construct()
    {
        
    }

    public function getMessage(): string
    {
        return $this->activityLog->getMessage();
    }

    public function getOccuredTimeString(): string
    {
        return $this->activityLog->getOccuredTimeString();
    }

}
