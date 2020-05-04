<?php

namespace Query\Application\Auth\Firm\Program;

interface CoordinatorRepository
{

    public function containRecordOfUnremovedCoordinatorCorrespondWithPersonnel(
            string $firmId, string $personnelId, string $programId): bool;
}
