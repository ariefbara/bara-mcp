<?php

namespace Client\Domain\Model\Client\ProgramParticipation\ConsultationSession;

use Client\Domain\Model\Client\ {
    ClientNotification,
    ProgramParticipation\ConsultationSession
};

class ConsultationSessionNotification
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
     * @var ClientNotification
     */
    protected $clientNotification;
    
    function __construct(ConsultationSession $consultationSession, string $id, ClientNotification $clientNotification)
    {
        $this->consultationSession = $consultationSession;
        $this->id = $id;
        $this->clientNotification = $clientNotification;
    }


}
