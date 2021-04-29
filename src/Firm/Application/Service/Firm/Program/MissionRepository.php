<?php

namespace Firm\Application\Service\Firm\Program;

use Firm\Domain\Model\Firm\Program\Mission;

interface MissionRepository
{

    public function nextIdentity(): string;

    public function add(Mission $mission): void;

    public function update(): void;

    public function ofId(ProgramCompositionId $programCompositionId, string $missionId): Mission;
    
    public function aMissionOfId(string $missionId): Mission;
}
