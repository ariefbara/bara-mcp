<?php

namespace Query\Domain\Model\User;

use DateTimeImmutable;
use Query\Domain\Model\ {
    User,
    Firm\Program\ConsultationSetup\ConsultationRequest,
    Firm\Program\ConsultationSetup\ConsultationSession,
    Firm\Program\Participant,
    Firm\Program\Participant\Worksheet\Comment
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
     * @var string
     */
    protected $message;

    /**
     *
     * @var bool
     */
    protected $read = false;

    /**
     *
     * @var Participant||null
     */
    protected $participant;

    /**
     *
     * @var DateTimeImmutable
     */
    protected $notifiedTime;

    /**
     *
     * @var ConsultationRequest||null
     */
    protected $consultationRequest = null;

    /**
     *
     * @var ConsultationSession||null
     */
    protected $consultationSession = null;

    /**
     *
     * @var Comment||null
     */
    protected $comment = null;

    function getClient(): User
    {
        return $this->client;
    }

    function getId(): string
    {
        return $this->id;
    }

    function getMessage(): string
    {
        return $this->message;
    }

    function isRead(): bool
    {
        return $this->read;
    }

    function getNotifiedTimeString(): string
    {
        return $this->notifiedTime->format('Y-m-d H:i:s');
    }

    function getConsultationRequest(): ?ConsultationRequest
    {
        return $this->consultationRequest;
    }

    function getConsultationSession(): ?ConsultationSession
    {
        return $this->consultationSession;
    }

    function getComment(): ?Comment
    {
        return $this->comment;
    }

    function getParticipant(): ?Participant
    {
        return $this->participant;
    }

    protected function __construct()
    {
        ;
    }

}
