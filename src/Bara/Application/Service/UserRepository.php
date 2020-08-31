<?php

namespace Bara\Application\Service;

use Bara\Domain\Model\User;

interface UserRepository
{
    public function ofId(string $userId): User;
}
