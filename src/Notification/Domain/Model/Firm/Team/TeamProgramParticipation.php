<?php

namespace Notification\Domain\Model\Firm\Team;

use Notification\Domain\Model\Program\Participant;

class TeamProgramParticipation
{
    /**
     *
     * @var Team
     */
    protected $team;
    /**
     *
     * @var string
     */
    protected $id;
    /**
     *
     * @var Participant
     */
    protected $programParticipation;
}
