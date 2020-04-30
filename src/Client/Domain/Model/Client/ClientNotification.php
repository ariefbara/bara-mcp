<?php

namespace Client\Domain\Model\Client;

use Client\Domain\Model\{
    Client,
    Client\ProgramParticipation\ConsultationRequest\ConsultationRequestNotification,
    Client\ProgramParticipation\ConsultationSession\ConsultationSessionNotification,
    Client\ProgramParticipation\ParticipantNotification,
    Client\ProgramParticipation\Worksheet\Comment\CommentNotification
};
use Shared\Domain\Model\Notification;

class ClientNotification
{

    /**
     *
     * @var Client
     */
    protected $client;

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
     * @var ParticipantNotification
     */
    protected $participantNotification = null;

    /**
     *
     * @var ConsultationSessionNotification
     */
    protected $consultationSessionNotification = null;

    /**
     *
     * @var ConsultationRequestNotification
     */
    protected $consultationRequestNotification = null;

    /**
     *
     * @var CommentNotification
     */
    protected $commentNotification = null;

    function __construct(Client $client, string $id, Notification $notification)
    {
        $this->client = $client;
        $this->id = $id;
        $this->notification = $notification;
    }

    public function read(): void
    {
        $this->notification->read();
    }

}
