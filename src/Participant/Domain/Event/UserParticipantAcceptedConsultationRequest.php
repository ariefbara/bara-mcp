<?php

namespace Participant\Domain\Event;

use Config\EventList;
use Notification\Application\Listener\Firm\Program\ConsultationSetup\ConsultationSessionApprovedByUserParticipantEventInterface;

class UserParticipantAcceptedConsultationRequest implements ConsultationSessionApprovedByUserParticipantEventInterface
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
    protected $consultationSessionId;

    public function getUserId(): string
    {
        return $this->userId;
    }

    public function getProgramParticipationId(): string
    {
        return $this->programParticipationId;
    }

    public function getConsultationSessionId(): string
    {
        return $this->consultationSessionId;
    }

    public function __construct(string $userId, string $programParticipationId, string $consultationSessionId)
    {
        $this->userId = $userId;
        $this->programParticipationId = $programParticipationId;
        $this->consultationSessionId = $consultationSessionId;
    }

    public function getName(): string
    {
        return EventList::USER_PARTICIPANT_ACCEPTED_CONSULTATION_REQUEST;
    }

}
