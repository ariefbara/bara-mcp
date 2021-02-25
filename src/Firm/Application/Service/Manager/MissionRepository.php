<?php

namespace Firm\Application\Service\Manager;

use Firm\Domain\Model\Firm\Program\Mission;

interface MissionRepository
{

    public function nextIdentity(): string;

    public function add(Mission $mission): void;

    public function aMissionOfId(string $missionId): Mission;

    public function update(): void;
}
