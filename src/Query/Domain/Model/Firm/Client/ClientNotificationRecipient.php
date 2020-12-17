<?php

namespace Query\Domain\Model\Firm\Client;

use DateTimeImmutable;
use Query\Domain\Model\Firm\Client;
use Query\Domain\Model\Firm\Program\ConsultationSetup\ConsultationRequest\ConsultationRequestNotification;
use Query\Domain\Model\Firm\Program\ConsultationSetup\ConsultationSession\ConsultationSessionNotification;
use Query\Domain\Model\Firm\Program\MeetingType\Meeting\MeetingAttendee\MeetingAttendeeNotification;
use Query\Domain\Model\Firm\Program\MeetingType\Meeting\MeetingNotification;
use Query\Domain\Model\Firm\Program\Participant\Worksheet\Comment\CommentNotification;
use Query\Domain\SharedModel\Notification;

class ClientNotificationRecipient
{

    /**
     *
     * @var Client
     */
    protected $client;

    /**
     *
     * @var Notification
     */
    protected $notification;

    /**
     *
     * @var string
     */
    protected $id;

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

    public function getClient(): Client
    {
        return $this->client;
    }

    public function getId(): string
    {
        return $this->id;
    }

    public function isRead(): bool
    {
        return $this->read;
    }

    protected function __construct()
    {
        
    }

    public function getNotifiedTimeString(): string
    {
        return $this->notifiedTime->format("Y-m-d H:i:s");
    }

    public function getMessage(): string
    {
        return $this->notification->getMessage();
    }

    public function getConsultationRequestNotification(): ?ConsultationRequestNotification
    {
        return $this->notification->getConsultationRequestNotification();
    }
    
    public function getConsultationSessionNotification(): ?ConsultationSessionNotification
    {
        return $this->notification->getConsultationSessionNotification();
    }

    public function getCommentNotification(): ?CommentNotification
    {
        return $this->notification->getCommentNotification();
    }
    
    public function getMeetingNotification(): ?MeetingNotification
    {
        return $this->notification->getMeetingNotification();
    }
    
    public function getMeetingAttendeeNotification(): ?MeetingAttendeeNotification
    {
        return $this->notification->getMeetingAttendeeNotification();
    }

}
