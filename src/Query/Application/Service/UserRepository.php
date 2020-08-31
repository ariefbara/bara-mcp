<?php

namespace Query\Application\Service;

use Firm\Application\Service\Firm\Program\UserRepository as InterfaceForFirmBC;
use Query\Domain\Model\User;

interface UserRepository extends InterfaceForFirmBC
{

    public function ofEmail(string $email): User;

    public function ofId(string $userId): User;

    public function all(int $page, int $pageSize);
}
