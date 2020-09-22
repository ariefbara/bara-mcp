<?php

namespace Notification\Domain\SharedModel;

use Notification\Domain\Model\ {
    Firm\Client,
    User
};

interface CanAddNotificationRecipientInterface
{

    public function addUserRecipient(User $user): void;

    public function addClientRecipient(Client $client): void;
}
