<?php

namespace Notification\Domain\Model;

interface CanBePersonalizeMailInterface
{
    public function appendRecipientNameInGreetings(string $recipientFirstName): void;
    
}
