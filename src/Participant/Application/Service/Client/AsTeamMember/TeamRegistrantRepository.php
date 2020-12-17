<?php

namespace Participant\Application\Service\Client\AsTeamMember;

use Participant\Domain\Model\TeamProgramRegistration;

interface TeamRegistrantRepository
{
    public function ofId(string $teamProgramRegistrationId): TeamProgramRegistration;
    
    public function update(): void;
}
