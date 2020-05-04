<?php

namespace Personnel\Domain\Event;

use Client\Application\Listener\ConsultantMutateConsultationSessionEventInterface;

class ConsultantMutateConsultationSessionEvent implements ConsultantMutateConsultationSessionEventInterface
{

    const EVENT_NAME = "ConsultantMutateConsultationSessionEvent";

    /**
     *
     * @var string
     */
    protected $firmId;

    /**
     *
     * @var string
     */
    protected $personnelId;

    /**
     *
     * @var string
     */
    protected $consultantId;

    /**
     *
     * @var string
     */
    protected $consultationSessionId;

    /**
     *
     * @var string
     */
    protected $messageForClient;

    function __construct(
            string $firmId, string $personnelId, string $consultantId, string $consultationSessionId,
            string $messageForClient)
    {
        $this->firmId = $firmId;
        $this->personnelId = $personnelId;
        $this->consultantId = $consultantId;
        $this->consultationSessionId = $consultationSessionId;
        $this->messageForClient = $messageForClient;
    }

    function getFirmId(): string
    {
        return $this->firmId;
    }

    function getPersonnelId(): string
    {
        return $this->personnelId;
    }

    function getConsultantId(): string
    {
        return $this->consultantId;
    }

    function getConsultationSessionId(): string
    {
        return $this->consultationSessionId;
    }

    function getMessageForClient(): string
    {
        return $this->messageForClient;
    }

    public function getName(): string
    {
        return self::EVENT_NAME;
    }

}
