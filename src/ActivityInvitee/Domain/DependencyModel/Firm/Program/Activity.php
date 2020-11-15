<?php

namespace ActivityInvitee\Domain\DependencyModel\Firm\Program;

class Activity
{

    /**
     *
     * @var string
     */
    protected $programId;

    /**
     *
     * @var string
     */
    protected $id;

    /**
     *
     * @var ActivityType
     */
    protected $activityType;

    /**
     *
     * @var bool
     */
    protected $cancelled;

    protected function __construct()
    {
        
    }

}
