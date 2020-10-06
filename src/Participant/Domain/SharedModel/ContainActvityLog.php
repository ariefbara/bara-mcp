<?php

namespace Participant\Domain\SharedModel;

use Participant\Domain\DependencyModel\Firm\Client\TeamMembership;

interface ContainActvityLog
{

    public function setOperator(TeamMembership $teamMember): void;
}
