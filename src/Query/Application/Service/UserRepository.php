<?php

namespace Query\Application\Service;

use Query\Domain\Model\User;

interface UserRepository
{

    public function ofEmail(string $email): User;

    public function ofId(string $userId): User;

    public function all(int $page, int $pageSize);
}
