<?php

namespace Participant\Application\Service;

use Participant\Domain\Model\Registrant\RegistrantProfile;

interface RegistrantProfileRepository
{

    public function aRegistrantProfileCorrespondWithProgramsProfileForm(
            string $programRegistrationId, string $programsProfileFormId): RegistrantProfile;

    public function update(): void;
}
