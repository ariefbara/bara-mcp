<?php

namespace Query\Application\Service\User;

use Query\Domain\Model\User\UserNotificationRecipient;

class ViewUserNotification
{
    /**
     *
     * @var UserNotificationRepository
     */
    protected $userNotificationRepository;
    
    public function __construct(UserNotificationRepository $userNotificationRepository)
    {
        $this->userNotificationRepository = $userNotificationRepository;
    }
    
    /**
     * 
     * @param string $userId
     * @param int $page
     * @param int $pageSize
     * @param bool|null $readStatus
     * @return UserNotificationRecipient[]
     */
    public function showAll(string $userId, int $page, int $pageSize, ?bool $readStatus)
    {
        return $this->userNotificationRepository->allNotificationBelongsToUser($userId, $page, $pageSize, $readStatus);
    }

}
