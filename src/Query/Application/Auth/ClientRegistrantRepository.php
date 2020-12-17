<?php

namespace Query\Application\Auth;

interface ClientRegistrantRepository
{

    public function containRecordOfUnconcludedRegistrationToProgram(
            string $firmId, string $clientId, string $programId): bool;
}
