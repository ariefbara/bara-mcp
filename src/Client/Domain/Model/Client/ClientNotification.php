<?php

namespace Client\Domain\Model\Client;

use Client\Domain\Model\ {
    Client,
    Client\ProgramParticipation\ConsultationRequest,
    Client\ProgramParticipation\ConsultationSession,
    Client\ProgramParticipation\Worksheet\Comment
};
use DateTimeImmutable;
use Resources\DateTimeImmutableBuilder;

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
     * @var DateTimeImmutable
     */
    protected $notifiedTime;

    /**
     *
     * @var ProgramParticipation||null
     */
    protected $programParticipation = null;

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

    protected function __construct(Client $client, string $id, string $message)
    {
        $this->client = $client;
        $this->id = $id;
        $this->message = $message;
        $this->read = false;
        $this->notifiedTime = DateTimeImmutableBuilder::buildYmdHisAccuracy();
    }

    public static function notificationForProgramParticipation(
            Client $client, string $id, string $message, ProgramParticipation $programParticipation): self
    {
        $clientNotification = new Static($client, $id, $message);
        $clientNotification->programParticipation = $programParticipation;
        return $clientNotification;
    }
    
    public static function notificationForConsultationRequest(
            Client $client, string $id, string $message, ConsultationRequest $consultationRequest): self
    {
        $clientNotification = new Static($client, $id, $message);
        $clientNotification->consultationRequest = $consultationRequest;
        return $clientNotification;
    }

    public static function notificationForConsultationSession(
            Client $client, string $id, string $message, ConsultationSession $consultationSession): self
    {
        $clientNotification = new Static($client, $id, $message);
        $clientNotification->consultationSession = $consultationSession;
        return $clientNotification;
    }

    public static function notificationForComment(
            Client $client, string $id, string $message, Comment $comment): self
    {
        $clientNotification = new Static($client, $id, $message);
        $clientNotification->comment = $comment;
        return $clientNotification;
    }

    public function read(): void
    {
        $this->read = true;
    }

}
