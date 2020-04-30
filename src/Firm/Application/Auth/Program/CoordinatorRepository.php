<?php

namespace Firm\Application\Auth\Program;

interface CoordinatorRepository
{

    public function containRecordOfUnremovedCoordinatorCorrespondWithPersonnel(
            string $firmId, string $personnelId, string $programId): bool;
}
