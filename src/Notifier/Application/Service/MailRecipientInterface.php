<?php

namespace Notifier\Application\Service;

interface MailRecipientInterface
{

    public function getRecipientName(): string;

    public function getRecipientMailAddress(): string;
}
