<?php

namespace Personnel\Domain\Event;

use Client\Application\Listener\ConsultantMutateConsultationRequestEventInterface;

class ConsultantMutateConsultationRequestEvent implements ConsultantMutateConsultationRequestEventInterface
{

    const EVENT_NAME = "ConsultantMutateConsultationRequestEvent";

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
    protected $consultationRequestId;

    /**
     *
     * @var string
     */
    protected $messageForClient;

    function __construct(
            string $firmId, string $personnelId, string $consultantId, string $consultationRequestId,
            string $messageForClient)
    {
        $this->firmId = $firmId;
        $this->personnelId = $personnelId;
        $this->consultantId = $consultantId;
        $this->consultationRequestId = $consultationRequestId;
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

    function getConsultationRequestId(): string
    {
        return $this->consultationRequestId;
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
