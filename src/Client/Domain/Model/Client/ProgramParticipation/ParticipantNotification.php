<?php

namespace Client\Domain\Model\Client\ProgramParticipation;

use Client\Domain\Model\Client\{
    ClientNotification,
    ProgramParticipation
};
use Shared\Domain\Model\Notification;

class ParticipantNotification
{

    /**
     *
     * @var ProgramParticipation
     */
    protected $programParticipation;

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

    function __construct(ProgramParticipation $programParticipation, string $id, ClientNotification $clientNotification)
    {
        $this->programParticipation = $programParticipation;
        $this->id = $id;
        $this->clientNotification = $clientNotification;
    }

}
