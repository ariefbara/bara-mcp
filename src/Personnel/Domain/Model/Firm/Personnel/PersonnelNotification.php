<?php

namespace Personnel\Domain\Model\Firm\Personnel;

use Personnel\Domain\Model\Firm\Personnel;
use Shared\Domain\Model\Notification;

class PersonnelNotification
{

    /**
     *
     * @var Personnel
     */
    protected $personnel;

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
    function __construct(Personnel $personnel, string $id, Notification $notification)
    {
        $this->personnel = $personnel;
        $this->id = $id;
        $this->notification = $notification;
    }
    
    public function read(): void
    {
        $this->notification->read();
    }

}
