<?php

namespace Client\Application\Listener;

use Client\Domain\Model\Client\ClientNotification;

interface ClientNotificationRepository
{

    public function nextIdentity(): string;

    public function add(ClientNotification $clientNotification): void;
}
