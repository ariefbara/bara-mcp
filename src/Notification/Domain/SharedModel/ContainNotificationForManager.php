<?php

namespace Notification\Domain\SharedModel;

interface ContainNotificationForManager
{
    public function addManagerRecipient(Manager $manager): void;
}
