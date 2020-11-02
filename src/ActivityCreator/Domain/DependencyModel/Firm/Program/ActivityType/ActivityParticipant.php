<?php

namespace ActivityCreator\Domain\DependencyModel\Firm\Program\ActivityType;

use ActivityCreator\Domain\DependencyModel\Firm\Program\ActivityType;
use SharedContext\Domain\ValueObject\{
    ActivityParticipantPriviledge,
    ActivityParticipantType
};

class ActivityParticipant
{

    /**
     *
     * @var ActivityType
     */
    protected $activityType;

    /**
     *
     * @var string
     */
    protected $id;

    /**
     *
     * @var ActivityParticipantType
     */
    protected $participantType;

    /**
     *
     * @var ActivityParticipantPriviledge
     */
    protected $participantPriviledge;

    protected function __construct()
    {
        
    }

}
