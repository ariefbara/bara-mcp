<?php

namespace Notification\Domain\SharedModel;

interface CanBePersonalizeMailInterface
{
    public function appendRecipientFirstNameInGreetings(string $recipientFirstName): void;
    
}
