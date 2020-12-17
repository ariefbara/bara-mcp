<?php

namespace Firm\Application\Service\Manager;

use Firm\Domain\Model\Firm\Program\Mission;

interface MissionRepository
{
    public function aMissionOfId(string $missionId): Mission;
    
    public function update(): void;
}
