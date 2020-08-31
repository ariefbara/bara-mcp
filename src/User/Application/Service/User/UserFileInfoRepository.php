<?php

namespace User\Application\Service\User;

use User\Domain\Model\User\UserFileInfo;

interface UserFileInfoRepository
{

    public function add(UserFileInfo $userFileInfo): void;

    public function nextIdentity(): string;
}
