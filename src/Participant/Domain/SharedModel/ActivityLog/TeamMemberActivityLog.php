<?php

namespace Participant\Domain\SharedModel\ActivityLog;

use Participant\Domain\{
    DependencyModel\Firm\Client\TeamMembership,
    SharedModel\ActivityLog
};

class TeamMemberActivityLog
{

    /**
     *
     * @var ActivityLog
     */
    protected $activityLog;

    /**
     *
     * @var string
     */
    protected $id;

    /**
     *
     * @var TeamMembership
     */
    protected $teamMember;

    public function __construct(ActivityLog $activityLog, string $id, TeamMembership $teamMember)
    {
        $this->activityLog = $activityLog;
        $this->id = $id;
        $this->teamMember = $teamMember;
    }

}
