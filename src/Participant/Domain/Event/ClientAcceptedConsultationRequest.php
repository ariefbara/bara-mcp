<?php

namespace Participant\Domain\Event;

use Config\EventList;
use Firm\Application\Listener\Firm\Program\ConsultationSetup\ClientAcceptedConsultationRequestEventInterface;

class ClientAcceptedConsultationRequest implements ClientAcceptedConsultationRequestEventInterface
{

    /**
     *
     * @var string
     */
    protected $firmId;

    /**
     *
     * @var string
     */
    protected $clientId;

    /**
     *
     * @var string
     */
    protected $programId;

    /**
     *
     * @var string
     */
    protected $consultationSessionId;

    public function getFirmId(): string
    {
        return $this->firmId;
    }

    public function getClientId(): string
    {
        return $this->clientId;
    }

    public function getProgramId(): string
    {
        return $this->programId;
    }

    public function getConsultationSessionId(): string
    {
        return $this->consultationSessionId;
    }

    public function __construct(string $firmId, string $clientId, string $programId, string $consultationSessionId)
    {
        $this->firmId = $firmId;
        $this->clientId = $clientId;
        $this->programId = $programId;
        $this->consultationSessionId = $consultationSessionId;
    }

    public function getName(): string
    {
        return EventList::CLIENT_ACCEPTED_CONSULTATION_REQUEST;
    }

}
