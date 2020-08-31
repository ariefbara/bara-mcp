<?php

namespace Participant\Domain\Event;

use Config\EventList;
use Firm\Application\Listener\Firm\Program\ConsultationSetup\UserAcceptedConsultationRequestEventInterface;

class UserAcceptedConsultationRequest implements UserAcceptedConsultationRequestEventInterface
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
    protected $consultationSessionId;

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

    public function getConsultationSessionId(): string
    {
        return $this->consultationSessionId;
    }

    public function __construct(string $userId, string $firmId, string $programId, string $consultationSessionId)
    {
        $this->userId = $userId;
        $this->firmId = $firmId;
        $this->programId = $programId;
        $this->consultationSessionId = $consultationSessionId;
    }

    public function getName(): string
    {
        return EventList::USER_ACCEPTED_CONSULTATION_REQUEST;
    }

}
