<?php

namespace Notification\Application\Service\User;

use Notification\Domain\Model\User\UserMail;

interface UserMailRepository
{

    public function nextIdentity(): string;

    public function add(UserMail $userMail): void;
}
