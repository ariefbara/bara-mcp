<?php

namespace Query\Application\Service\User;

use Query\Domain\Model\User;

interface UserRepository
{
    public function ofId(string $userId): User;
}
