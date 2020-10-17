<?php

namespace Personnel\Domain\Model\Firm\Personnel\ProgramConsultant\ConsultationRequest;

use Personnel\Domain\{
    Model\Firm\Personnel\ProgramConsultant,
    Model\Firm\Personnel\ProgramConsultant\ConsultationRequest,
    SharedModel\ActivityLog
};

class ConsultationRequestActivityLog
{

    /**
     *
     * @var ConsultationRequest
     */
    protected $consultationRequest;

    /**
     *
     * @var string
     */
    protected $id;

    /**
     *
     * @var ActivityLog
     */
    protected $activityLog;

    public function __construct(
            ConsultationRequest $consultationRequest, string $id, string $message, ProgramConsultant $consultant)
    {
        $this->consultationRequest = $consultationRequest;
        $this->id = $id;
        $this->activityLog = new ActivityLog($id, $message, $consultant);
    }

}
