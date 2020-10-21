<?php

namespace Notification\Application\Service\User;

use Notification\Domain\Model\User;

interface UserRepository
{

    public function ofId(string $userId): User;
}
