<?php

namespace Query\Application\Service\Firm\Team;

use Query\Domain\Model\Firm\Program\Registrant\RegistrantProfile;

interface RegistrantProfileRepository
{

    public function aRegistrantProfileBelongsToTeamCorrespondWithProgramsProfileForm(
            string $firmId, string $teamId, string $programRegistrationId, string $programsProfileFormId): RegistrantProfile;

    public function allRegistrantProfilesBelongsToTeamCorrespondWithProgramsProfileForm(
            string $firmId, string $teamId, string $programRegistrationId, int $page, int $pageSize);
}
