<?php

namespace Team\Application\Service\Team;

use Team\Domain\Model\Team\TeamFileInfo;

interface TeamFileInfoRepository
{

    public function nextIdentity(): string;

    public function add(TeamFileInfo $teamFileInfo): void;
}
