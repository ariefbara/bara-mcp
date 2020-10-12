<?php

namespace Query\Application\Service\Firm\Client;

interface ClientNotificationRepository
{

    public function allNotificationsBelongsToClient(
            string $firmId, string $clientId, int $page, int $pageSize, ?bool $readStatus);
}
