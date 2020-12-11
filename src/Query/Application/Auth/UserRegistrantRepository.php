<?php

namespace Query\Application\Auth;

interface UserRegistrantRepository
{

    public function containRecordOfUnconcludedRegistrationToProgram(
            string $userId, string $firmId, string $programId): bool;
}
