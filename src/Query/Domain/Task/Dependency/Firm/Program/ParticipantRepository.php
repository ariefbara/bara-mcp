<?php

namespace Query\Domain\Task\Dependency\Firm\Program;

use Query\Domain\Model\Firm\Program\Participant;

interface ParticipantRepository
{

    public function allActiveIndividualAndTeamProgramParticipationBelongsToClient(string $clientId);

    public function allProgramParticipantsInFirm(string $firmId, ParticipantFilter $filter);

    public function aProgramParticipantInFirm(string $firmId, string $id): Participant;
}
