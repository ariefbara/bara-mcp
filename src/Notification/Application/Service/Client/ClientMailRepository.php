<?php

namespace Notification\Application\Service\Client;

use Notification\Domain\Model\Firm\Client\ClientMail;

interface ClientMailRepository
{

    public function nextIdentity(): string;

    public function add(ClientMail $clientMail): void;
}
