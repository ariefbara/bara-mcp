<?php

namespace Firm\Application\Service\Client\AsTeamMember\AsProgramParticipant;

use Firm\Domain\Model\Firm\Program\TeamParticipant;

interface TeamParticipantRepository
{

    public function aTeamParticipantCorrespondWitnProgram(string $teamId, string $programId): TeamParticipant;

    public function ofId(string $id): TeamParticipant;
}
