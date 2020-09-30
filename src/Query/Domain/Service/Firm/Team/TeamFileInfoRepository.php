<?php

namespace Query\Domain\Service\Firm\Team;

use Query\Domain\Model\Firm\Team\TeamFileInfo;

interface TeamFileInfoRepository
{

    public function aFileInfoBelongsToTeam(string $teamId, string $teamFileInfoId): TeamFileInfo;
}
