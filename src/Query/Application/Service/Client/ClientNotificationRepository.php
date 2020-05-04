<?php

namespace Query\Application\Service\Client;

use Query\Domain\Model\Client\ClientNotification;

interface ClientNotificationRepository
{

    public function ofId(string $clientId, string $clientNotificationId): ClientNotification;

    public function all(string $clientId, int $page, int $pageSize);
}
