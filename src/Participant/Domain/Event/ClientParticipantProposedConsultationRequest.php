<?php

namespace Participant\Domain\Event;

use Config\EventList;
use Notification\Application\Listener\Firm\Program\ConsultationSetup\ConsultationRequestChangedByClientParticipantEventInterface;

class ClientParticipantProposedConsultationRequest implements ConsultationRequestChangedByClientParticipantEventInterface
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
    protected $programParticipationId;

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

    public function getProgramParticipationId(): string
    {
        return $this->programParticipationId;
    }

    public function getConsultationRequestId(): string
    {
        return $this->consultationRequestId;
    }

    public function __construct(
            string $firmId, string $clientId, string $programParticipationId, string $consultationRequestId)
    {
        $this->firmId = $firmId;
        $this->clientId = $clientId;
        $this->programParticipationId = $programParticipationId;
        $this->consultationRequestId = $consultationRequestId;
    }

    public function getName(): string
    {
        return EventList::CLIENT_PARTICIPANT_PROPOSED_CONSULTATION_REQUEST;
    }

}
