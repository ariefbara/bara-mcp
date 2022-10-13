<?php

namespace Personnel\Domain\Task\Mentor;

use Personnel\Domain\Model\Firm\Personnel\ProgramConsultant\ConsultationRequestData;

class ProposeConsultationPayload
{

    /**
     * 
     * @var string
     */
    protected $participantId;

    /**
     * 
     * @var string
     */
    protected $consultationSetupId;

    /**
     * 
     * @var ConsultationRequestData
     */
    protected $consultationRequestData;

    /**
     * 
     * @var string|null
     */
    public $proposedConsultationRequestId;

    public function getParticipantId(): string
    {
        return $this->participantId;
    }

    public function getConsultationSetupId(): string
    {
        return $this->consultationSetupId;
    }

    public function getConsultationRequestData(): ConsultationRequestData
    {
        return $this->consultationRequestData;
    }

    public function __construct(
            string $participantId, string $consultationSetupId, ConsultationRequestData $consultationRequestData)
    {
        $this->participantId = $participantId;
        $this->consultationSetupId = $consultationSetupId;
        $this->consultationRequestData = $consultationRequestData;
    }

}
