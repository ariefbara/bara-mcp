<?php

namespace Participant\Application\Service\User;

use Participant\Domain\Model\UserRegistrant;

interface UserRegistrantRepository
{
    public function aUserRegistrant(string $userId, string $programRegistrationId): UserRegistrant;
    
    public function update(): void;
}
