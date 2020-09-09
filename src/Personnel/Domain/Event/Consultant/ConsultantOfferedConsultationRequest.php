<?php

namespace Personnel\Domain\Event\Consultant;

use Config\EventList;
use Notification\Application\Listener\Firm\Program\ConsultationSetup\ConsultationRequestChangedByConsultantEventInterface;

class ConsultantOfferedConsultationRequest implements ConsultationRequestChangedByConsultantEventInterface
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
    protected $consultationRequestId;

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

    public function getConsultationRequestId(): string
    {
        return $this->consultationRequestId;
    }

    public function __construct(string $firmId, string $personnelId, string $programConsultationId,
            string $consultationRequestId)
    {
        $this->firmId = $firmId;
        $this->personnelId = $personnelId;
        $this->programConsultationId = $programConsultationId;
        $this->consultationRequestId = $consultationRequestId;
    }

    public function getName(): string
    {
        return EventList::CONSULTANT_OFFERED_CONSULTATION_REQUEST;
    }

}
