<?php

namespace Query\Application\Service\Firm\Client;

use Query\Domain\Model\Firm\Program\Participant\ParticipantProfile;

interface ParticipantProfileRepository
{

    public function allParticipantProfilesBelongsToParticipant(
            string $firmId, string $clientId, string $programParticipationId, int $page, int $pageSize);

    public function aParticipantProfileBelongsToClientCorrespondWithProgramsProfileForm(
            string $firmId, string $clientId, string $programParticipationId, string $programsProfileFormId): ParticipantProfile;
}
