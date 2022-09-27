<?php

namespace Query\Domain\Task\InProgram;

use Query\Domain\Task\Dependency\ActivityInvitationFilter;

class ViewAllActivityInvitationsToProgramPartipantPayload
{

    /**
     * 
     * @var string
     */
    protected $participantId;

    /**
     * 
     * @var ActivityInvitationFilter
     */
    protected $activityInvitationFilter;
    public $result;

    public function getParticipantId(): string
    {
        return $this->participantId;
    }

    public function getActivityInvitationFilter(): ActivityInvitationFilter
    {
        return $this->activityInvitationFilter;
    }

    public function __construct(string $participantId, ActivityInvitationFilter $activityInvitationFilter)
    {
        $this->participantId = $participantId;
        $this->activityInvitationFilter = $activityInvitationFilter;
    }

}
