<?php

namespace Personnel\Domain\Model\Firm\Personnel\ProgramConsultant\ConsultationRequest;

use Personnel\Domain\Model\Firm\Personnel\{
    PersonnelNotification,
    ProgramConsultant\ConsultationRequest
};

class PersonnelNotificationOnConsultationRequest
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
     * @var PersonnelNotification
     */
    protected $personnelNotification;

    function __construct(
            ConsultationRequest $consultationRequest, string $id, PersonnelNotification $personnelNotification)
    {
        $this->consultationRequest = $consultationRequest;
        $this->id = $id;
        $this->personnelNotification = $personnelNotification;
    }

}
