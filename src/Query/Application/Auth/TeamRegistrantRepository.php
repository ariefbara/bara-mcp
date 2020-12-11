<?php

namespace Query\Application\Auth;

interface TeamRegistrantRepository
{
    public function containRecordOfUnconcludedRegistrationToProgram(
            string $firmId, string $teamId, string $programId): bool;
}
