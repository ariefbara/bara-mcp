<?php

namespace Participant\Domain\DependencyModel\Firm\Client;

use Participant\Domain\DependencyModel\Firm\Team;

interface AssetBelongsToTeamInterface
{
    public function belongsToTeam(Team $team): bool;
}
