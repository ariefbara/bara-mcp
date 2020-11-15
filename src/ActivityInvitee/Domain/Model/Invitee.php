<?php

namespace ActivityInvitee\Domain\Model;

use ActivityInvitee\Domain\ {
    DependencyModel\Firm\Program\Activity,
    Model\Invitee\InviteeReport
};

class Invitee
{

    /**
     *
     * @var Activity
     */
    protected $activity;

    /**
     *
     * @var string
     */
    protected $id;

    /**
     *
     * @var bool|null
     */
    protected $willAttend;

    /**
     *
     * @var bool|null
     */
    protected $attended;

    /**
     *
     * @var bool
     */
    protected $removed;
    
    /**
     *
     * @var InviteeReport|null
     */
    protected $inviteeReport;

    protected function __construct()
    {
        
    }

}
