<?php

namespace Notification\Application\Service;

interface RecipientRepository
{

    public function allRecipientsWithZeroAttempt();

    public function update(): void;
}
