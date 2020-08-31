<?php

namespace Participant\Domain\Event;

use Config\EventList;
use Firm\Application\Listener\Firm\Program\ConsultationSetup\UserUpdatedConsultationRequestEventInterface;

class UserProposedConsultationRequest implements UserUpdatedConsultationRequestEventInterface
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
    protected $firmId;

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

    public function getUserId(): string
    {
        return $this->userId;
    }

    public function getFirmId(): string
    {
        return $this->firmId;
    }

    public function getProgramId(): string
    {
        return $this->programId;
    }

    public function getConsultationRequestId(): string
    {
        return $this->consultationRequestId;
    }

    public function __construct(string $userId, string $firmId, string $programId, string $consultationRequestId)
    {
        $this->userId = $userId;
        $this->firmId = $firmId;
        $this->programId = $programId;
        $this->consultationRequestId = $consultationRequestId;
    }

    public function getName(): string
    {
        return EventList::USER_PROPOSED_CONSULTATION_REQUEST;
    }

}
