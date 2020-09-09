<?php

namespace Personnel\Domain\Event\Consultant;

use Config\EventList;
use Notification\Application\Listener\Firm\Program\ConsultationSetup\ConsultationSessionApprovedByConsultantEventInterface;

class ConsultantAcceptedConsultationRequest implements ConsultationSessionApprovedByConsultantEventInterface
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
    protected $personnelId;

    /**
     *
     * @var string
     */
    protected $programConsultationId;

    /**
     *
     * @var string
     */
    protected $consultationSessionId;

    public function getFirmId(): string
    {
        return $this->firmId;
    }

    public function getPersonnelId(): string
    {
        return $this->personnelId;
    }

    public function getProgramConsultationId(): string
    {
        return $this->programConsultationId;
    }

    public function getConsultationSessionId(): string
    {
        return $this->consultationSessionId;
    }

    public function __construct(
            string $firmId, string $personnelId, string $programConsultationId, string $consultationSessionId)
    {
        $this->firmId = $firmId;
        $this->personnelId = $personnelId;
        $this->programConsultationId = $programConsultationId;
        $this->consultationSessionId = $consultationSessionId;
    }

    public function getName(): string
    {
        return EventList::CONSULTANT_ACCEPTED_CONSULTATION_REQUEST;
    }

}
