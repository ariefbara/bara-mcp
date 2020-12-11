<?php

namespace Query\Application\Service\Firm\Program;

use Query\Domain\Model\Firm\Program\Registrant\RegistrantProfile;

interface RegistrantProfileRepository
{

    public function aRegistrantProfileInProgram(string $firmId, string $programId, string $registrantProfileId): RegistrantProfile;

    public function allRegistrantProfilesInBelongsToRegistrant(
            string $firmId, string $programId, string $registrantId, int $page, int $pageSize);
}
