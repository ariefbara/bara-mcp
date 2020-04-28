<?php

namespace Personnel\Domain\Model\Firm\Personnel\ProgramConsultant\ConsultationSession;

use Personnel\Domain\Model\Firm\Personnel\ {
    PersonnelNotification,
    ProgramConsultant\ConsultationSession
};

class PersonnelNotificationOnConsultationSession
{

    /**
     *
     * @var ConsultationSession
     */
    protected $consultationSession;

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
            ConsultationSession $consultationSession, string $id, PersonnelNotification $personnelNotification)
    {
        $this->consultationSession = $consultationSession;
        $this->id = $id;
        $this->personnelNotification = $personnelNotification;
    }

}
