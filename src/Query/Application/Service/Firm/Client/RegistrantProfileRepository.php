<?php

namespace Query\Application\Service\Firm\Client;

use Query\Domain\Model\Firm\Program\Registrant\RegistrantProfile;

interface RegistrantProfileRepository
{

    public function aRegistrantProfileBelongsToClientCorrespondWithProgramsProfileForm(
            string $firmId, string $clientId, string $programRegistrationId, string $programsProfileFormId): RegistrantProfile;

    public function allRegistrantProfilesInProgramRegistrationBelongsToClient(
            string $firmId, string $clientId, string $programRegistrationId, int $page, int $pageSize);
}
