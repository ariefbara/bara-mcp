<?php

namespace Participant\Domain\Event;

use Config\EventList;
use Firm\Application\Listener\Firm\Program\ConsultationSetup\ClientUpdatedConsultationRequestEventInterface;

class ClientProposedConsultationRequest implements ClientUpdatedConsultationRequestEventInterface
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
    protected $consultationRequestId;

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

    public function getConsultationRequestId(): string
    {
        return $this->consultationRequestId;
    }

    public function __construct(string $firmId, string $clientId, string $programId, string $consultationRequestId)
    {
        $this->firmId = $firmId;
        $this->clientId = $clientId;
        $this->programId = $programId;
        $this->consultationRequestId = $consultationRequestId;
    }

    public function getName(): string
    {
        return EventList::CLIENT_PROPOSED_CONSULTATION_REQUEST;
    }

}
