<?php

namespace Client\Application\Service\Client;

use Client\Domain\Model\Client\ProgramRegistration;

interface ProgramRegistrationRepository
{

    public function add(ProgramRegistration $programRegistration): void;

    public function update(): void;

    public function ofId(string $clientId, string $programRegistrationId): ProgramRegistration;

    public function all(string $clientId, int $page, int $pageSize);
}
