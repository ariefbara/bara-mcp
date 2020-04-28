<?php

namespace Client\Domain\Event;

use Personnel\Application\Listener\EventInterfaceForPersonnelNotification;

class ConsultationRequestMutatedByParticipantEvent implements EventInterfaceForPersonnelNotification
{

    const EVENT_NAME = "ConsultationRequestMutatedByParticipantEvent";

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

    function getConsultationRequestId(): string
    {
        return $this->consultationRequestId;
    }

    function __construct(string $consultationRequestId, string $messageForPersonnel)
    {
        $this->consultationRequestId = $consultationRequestId;
        $this->messageForPersonnel = $messageForPersonnel;
    }

    public function getName(): string
    {
        return self::EVENT_NAME;
    }

    public function getId(): string
    {
        return $this->consultationRequestId;
    }

    public function getMessageForPersonnel(): string
    {
        return $this->messageForPersonnel;
    }

}
