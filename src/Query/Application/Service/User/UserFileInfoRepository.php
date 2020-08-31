<?php

namespace Query\Application\Service\User;

use Query\Domain\Model\User\UserFileInfo;

interface UserFileInfoRepository
{
    public function ofId(string $userId, string $userFileInfoId): UserFileInfo;
}
