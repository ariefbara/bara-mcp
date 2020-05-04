<?php

namespace Client\Domain\Event;

use Personnel\Application\Listener\ParticipantMutateConsultationRequestEventInterface;

class ParticipantMutateConsultationRequestEvent implements ParticipantMutateConsultationRequestEventInterface
{

    const EVENT_NAME = "ParticipantMutateConsultationRequestEvent";

    /**
     *
     * @var string
     */
    protected $clientId;

    /**
     *
     * @var string
     */
    protected $participantId;

    /**
     *
     * @var string
     */
    protected $consultationRequestId;

    /**
     *
     * @var string
     */
    protected $messageForPersonnel;

    function getClientId(): string
    {
        return $this->clientId;
    }

    function getParticipantId(): string
    {
        return $this->participantId;
    }

    function getConsultationRequestId(): string
    {
        return $this->consultationRequestId;
    }

    function getMessageForPersonnel(): string
    {
        return $this->messageForPersonnel;
    }

    function __construct(string $clientId, string $participantId, string $consultationRequestId,
            string $messageForPersonnel)
    {
        $this->clientId = $clientId;
        $this->participantId = $participantId;
        $this->consultationRequestId = $consultationRequestId;
        $this->messageForPersonnel = $messageForPersonnel;
    }

    public function getName(): string
    {
        return self::EVENT_NAME;
    }

}
