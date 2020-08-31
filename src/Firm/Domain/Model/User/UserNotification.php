<?php

namespace Firm\Domain\Model\User;

use Firm\Domain\Model\{
    SharedEntity\Notification,
    User
};

class UserNotification
{

    /**
     *
     * @var User
     */
    protected $user;

    /**
     *
     * @var string
     */
    protected $id;

    /**
     *
     * @var Notification
     */
    protected $notification;
    
    public function __construct(User $user, string $id, string $message)
    {
        $this->user = $user;
        $this->id = $id;
        $this->notification = $notification;
    }


}
