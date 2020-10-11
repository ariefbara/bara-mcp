<?php

namespace Notification\Domain\SharedModel;

use Notification\Domain\Model\ {
    Firm\Client,
    Firm\Personnel,
    User
};

interface ContainNotification
{

    public function addUserRecipient(User $user): void;

    public function addClientRecipient(Client $client): void;

    public function addPersonnelRecipient(Personnel $personnel): void;
}
