<?php

namespace Client\Application\Service\Client;

use Client\Domain\Model\Client\ClientNotification;

interface ClientNotificationRepository
{

    public function ofId(string $clientId, string $clientNotificationId): ClientNotification;

    public function all(string $clientId, int $page, int $pageSize);
}
