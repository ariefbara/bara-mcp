<?php

namespace Participant\Application\Service;

use Participant\Domain\Model\TeamProgramRegistration;

interface TeamProgramRegistrationRepository
{

    public function nextIdentity(): string;

    public function add(TeamProgramRegistration $teamProgramRegistration): void;

    public function ofId(string $teamProgramRegistrationId): TeamProgramRegistration;
    
    public function update(): void;
}
