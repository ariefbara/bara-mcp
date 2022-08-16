<?php

namespace Firm\Domain\Task\Dependency\Firm\Team;

use Firm\Domain\Model\Firm\Team\TeamParticipant;

interface TeamParticipantRepository
{

    public function nextIdentity(): string;

    public function add(TeamParticipant $teamParticipant): void;
}
