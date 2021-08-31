<?php

namespace Query\Domain\Task\Dependency\Firm\Program;

interface ParticipantRepository
{

    public function allActiveIndividualAndTeamProgramParticipationBelongsToClient(string $clientId);
}
