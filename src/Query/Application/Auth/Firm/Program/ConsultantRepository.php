<?php

namespace Query\Application\Auth\Firm\Program;

interface ConsultantRepository
{

    public function containRecordOfUnremovedConsultantCorrespondWithPersonnel(
            string $firmId, string $personnelId, string $programId): bool;
}
