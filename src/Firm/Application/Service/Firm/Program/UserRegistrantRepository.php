<?php

namespace Firm\Application\Service\Firm\Program;

use Firm\Domain\Model\Firm\Program\UserRegistrant;

interface UserRegistrantRepository
{

    public function ofId(string $firmId, string $programId, string $userRegistrantId): UserRegistrant;

    public function update(): void;
}
