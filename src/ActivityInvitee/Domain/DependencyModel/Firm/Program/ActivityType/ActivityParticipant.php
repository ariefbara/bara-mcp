<?php

namespace ActivityInvitee\Domain\DependencyModel\Firm\Program\ActivityType;

use ActivityInvitee\Domain\DependencyModel\Firm\ {
    FeedbackForm,
    Program\ActivityType
};
use SharedContext\Domain\ValueObject\ {
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

    /**
     *
     * @var FeedbackForm|null
     */
    protected $reportForm;

    protected function __construct()
    {
        
    }

}
