<?php

namespace User\Application\Service\User;

use User\Domain\Model\User\ProgramRegistration;

interface ProgramRegistrationRepository
{

    public function nextIdentity(): string;

    public function add(ProgramRegistration $programRegistration): void;

    public function ofId(string $userId, string $programRegistrationId): ProgramRegistration;

    public function update(): void;
}
