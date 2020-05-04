<?php

namespace Query\Application\Service\Client;

use Query\Domain\Model\Client\ClientNotification;

class ClientNotificationView
{

    /**
     *
     * @var ClientNotificationRepository
     */
    protected $clientNotificationRepository;

    function __construct(ClientNotificationRepository $clientNotificationRepository)
    {
        $this->clientNotificationRepository = $clientNotificationRepository;
    }

    public function showById(string $clientId, string $clientNotificationId): ClientNotification
    {
        return $this->clientNotificationRepository->ofId($clientId, $clientNotificationId);
    }

    /**
     * 
     * @param string $clientId
     * @param int $page
     * @param int $pageSize
     * @return ClientNotification[]
     */
    public function showAll(string $clientId, int $page, int $pageSize)
    {
        return $this->clientNotificationRepository->all($clientId, $page, $pageSize);
    }

}
