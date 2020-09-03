<?php

namespace Participant\Domain\Event;

use Config\EventList;
use Notification\Application\Listener\Firm\Program\ConsultationSetup\ConsultationRequestChangedByUserParticipantEventInterface;

class UserParticipantProposedConsultationRequest implements ConsultationRequestChangedByUserParticipantEventInterface
{

    /**
     *
     * @var string
     */
    protected $userId;

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

    public function getUserId(): string
    {
        return $this->userId;
    }

    public function getProgramParticipationId(): string
    {
        return $this->programParticipationId;
    }

    public function getConsultationRequestId(): string
    {
        return $this->consultationRequestId;
    }

    public function __construct(string $userId, string $programParticipationId, string $consultationRequestId)
    {
        $this->userId = $userId;
        $this->programParticipationId = $programParticipationId;
        $this->consultationRequestId = $consultationRequestId;
    }

    public function getName(): string
    {
        return EventList::USER_PARTICIPANT_PROPOSED_CONSULTATION_REQUEST;
    }

}
