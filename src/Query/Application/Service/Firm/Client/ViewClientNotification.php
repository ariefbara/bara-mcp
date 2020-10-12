<?php

namespace Query\Application\Service\Firm\Client;

use Query\Domain\Model\Firm\Client\ClientNotificationRecipient;

class ViewClientNotification
{
    /**
     *
     * @var ClientNotificationRepository
     */
    protected $clientNotificationRecipientRepository;
    
    public function __construct(ClientNotificationRepository $clientNotificationRecipientRepository)
    {
        $this->clientNotificationRecipientRepository = $clientNotificationRecipientRepository;
    }
    
    /**
     * 
     * @param string $firmId
     * @param string $clientId
     * @param string $page
     * @param int $pageSize
     * @param bool|null $readStatus
     * @return ClientNotificationRecipient[]
     */
    public function showAll(string $firmId, string $clientId, string $page, int $pageSize, ?bool $readStatus)
    {
        return $this->clientNotificationRecipientRepository->allNotificationsBelongsToClient(
                $firmId, $clientId, $page, $pageSize, $readStatus);
    }

}
