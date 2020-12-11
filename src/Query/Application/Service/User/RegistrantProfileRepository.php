<?php

namespace Query\Application\Service\User;

use Query\Domain\Model\Firm\Program\Registrant\RegistrantProfile;

interface RegistrantProfileRepository
{
    public function aRegistrantProfileBelongsToUserCorrespondWithProgramsProfileForm(
            string $userId, string $programRegistrationId, string $programsProfileFormId): RegistrantProfile;
    
    public function allRegistrantProfilesBelongsToUser(
            string $userId, string $programRegistrationId, int $page, int $pageSize);
}
