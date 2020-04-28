<?php

namespace Client\Domain\Event;

use Personnel\Application\Listener\EventInterfaceForPersonnelNotification;

class ConsultationSessionMutatedByParticipantEvent implements EventInterfaceForPersonnelNotification
{

    const EVENT_NAME = "ConsultationSessionMutatedByParticipantEvent";

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

    function getConsultationSessionId(): string
    {
        return $this->consultationSessionId;
    }

    function __construct(string $consultationSessionId, string $messageForPersonnel)
    {
        $this->consultationSessionId = $consultationSessionId;
        $this->messageForPersonnel = $messageForPersonnel;
    }

    public function getName(): string
    {
        return self::EVENT_NAME;
    }

    public function getId(): string
    {
        return $this->consultationSessionId;
    }

    public function getMessageForPersonnel(): string
    {
        return $this->messageForPersonnel;
    }

}
