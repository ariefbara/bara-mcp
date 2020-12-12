<?php

namespace Query\Domain\Model\Firm\Program\Coordinator;

use DateTimeImmutable;
use Query\Domain\Model\Firm\Program\ConsultationSetup\ConsultationSession\ConsultationSessionNotification;
use Query\Domain\Model\Firm\Program\Coordinator;
use Query\Domain\SharedModel\Notification;

class CoordinatorNotificationRecipient
{

    /**
     * 
     * @var Coordinator
     */
    protected $coordinator;

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

    /**
     * 
     * @var bool
     */
    protected $read;

    /**
     * 
     * @var DateTimeImmutable
     */
    protected $notifiedTime;

    function getCoordinator(): Coordinator
    {
        return $this->coordinator;
    }

    function getId(): string
    {
        return $this->id;
    }

    function getNotification(): Notification
    {
        return $this->notification;
    }

    function isRead(): bool
    {
        return $this->read;
    }

    function getNotifiedTimeString(): string
    {
        return $this->notifiedTime->format("Y-m-d H:i:s");
    }

    protected function __construct()
    {
        
    }

    public function getConsultationSessionNotification(): ?ConsultationSessionNotification
    {
        return $this->notification->getConsultationSessionNotification();
    }

}
