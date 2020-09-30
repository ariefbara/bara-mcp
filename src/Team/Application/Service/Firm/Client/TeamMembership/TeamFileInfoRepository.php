<?php

namespace Team\Application\Service\Firm\Client\TeamMembership;

use Team\Domain\Model\Team\TeamFileInfo;

interface TeamFileInfoRepository
{

    public function nextIdentity(): string;

    public function add(TeamFileInfo $teamFileInfo): void;
}
