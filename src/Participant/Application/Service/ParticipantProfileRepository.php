<?php

namespace Participant\Application\Service;

use Participant\Domain\Model\Participant\ParticipantProfile;

interface ParticipantProfileRepository
{

    public function aParticipantProfileCorrespondWithProgramsProfileForm(
            string $programParticipationId, string $programsProfileFormId): ParticipantProfile;

    public function update(): void;
}
