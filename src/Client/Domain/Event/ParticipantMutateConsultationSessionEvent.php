<?php

namespace Client\Domain\Event;

use Personnel\Application\Listener\ParticipantMutateConsultationSessionEventInterface;

class ParticipantMutateConsultationSessionEvent implements ParticipantMutateConsultationSessionEventInterface
{

    const EVENT_NAME = "ConsultationSessionMutatedByParticipantEvent";

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
    protected $consultationSessionId;

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

    function getConsultationSessionId(): string
    {
        return $this->consultationSessionId;
    }

    function getMessageForPersonnel(): string
    {
        return $this->messageForPersonnel;
    }

    function __construct(string $clientId, string $participantId, string $consultationSessionId,
            string $messageForPersonnel)
    {
        $this->clientId = $clientId;
        $this->participantId = $participantId;
        $this->consultationSessionId = $consultationSessionId;
        $this->messageForPersonnel = $messageForPersonnel;
    }

    public function getName(): string
    {
        return self::EVENT_NAME;
    }

}
