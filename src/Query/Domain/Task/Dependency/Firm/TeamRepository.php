<?php

namespace Query\Domain\Task\Dependency\Firm;

use Query\Domain\Model\Firm\Team;

interface TeamRepository
{
    public function allTeamInFirm(string $firmId, TeamFilter $teamFilter);
    
    public function aTeamInFirm(string $firmId, string $id): Team;
}
