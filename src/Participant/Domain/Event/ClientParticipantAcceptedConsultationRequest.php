<?php

namespace Participant\Domain\Event;

use Config\EventList;
use Notification\Application\Listener\Firm\Program\ConsultationSetup\ConsultationSessionApprovedByClientParticipantEventInterface;

class ClientParticipantAcceptedConsultationRequest implements ConsultationSessionApprovedByClientParticipantEventInterface
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
    protected $consultationSessionId;

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

    public function getConsultationSessionId(): string
    {
        return $this->consultationSessionId;
    }

    public function __construct(
            string $firmId, string $clientId, string $programParticipationId, string $consultationSessionId)
    {
        $this->firmId = $firmId;
        $this->clientId = $clientId;
        $this->programParticipationId = $programParticipationId;
        $this->consultationSessionId = $consultationSessionId;
    }

    public function getName(): string
    {
        return EventList::CLIENT_PARTICIPANT_ACCEPTED_CONSULTATION_REQUEST;
    }

}
