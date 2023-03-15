<?php

namespace Firm\Domain\Task\Dependency\Firm\Program;

use Firm\Domain\Model\Firm\Program\Mission;

interface MissionRepository
{

    public function nextIdentity(): string;

    public function add(Mission $mission): void;

    public function aMissionOfId(string $missionId): Mission;
}
