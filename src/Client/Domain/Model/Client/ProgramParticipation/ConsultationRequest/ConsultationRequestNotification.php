<?php

namespace Client\Domain\Model\Client\ProgramParticipation\ConsultationRequest;

use Client\Domain\Model\Client\{
    ClientNotification,
    ProgramParticipation\ConsultationRequest
};

class ConsultationRequestNotification
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
     * @var ClientNotification
     */
    protected $clientNotification;

    function __construct(ConsultationRequest $consultationRequest, string $id, ClientNotification $clientNotification)
    {
        $this->consultationRequest = $consultationRequest;
        $this->id = $id;
        $this->clientNotification = $clientNotification;
    }

}
