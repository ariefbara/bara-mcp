<?php

namespace Notification\Domain\Model\Firm\Client\ClientProgramParticipation;

use Notification\Domain\Model\{
    Firm\Client\ClientMailNotification,
    Firm\Client\ClientProgramParticipation,
    Firm\Program\Participant\ParticipantMailNotification,
    SharedEntity\KonsultaMailMessage
};
use Resources\Application\Service\{
    Mailer,
    SenderInterface
};

class ClientProgramParticipationMailNotification
{

    /**
     *
     * @var ClientProgramParticipation
     */
    protected $clientProgramParticipation;

    /**
     *
     * @var ClientMailNotification
     */
    protected $clientMailNotification;

    /**
     *
     * @var ParticipantMailNotification
     */
    protected $programParticipationMailNotification;

    public function __construct(
            ClientProgramParticipation $clientProgramParticipation, ClientMailNotification $clientMailNotification,
            ParticipantMailNotification $programParticipationMailNotification)
    {
        $this->clientProgramParticipation = $clientProgramParticipation;
        $this->clientMailNotification = $clientMailNotification;
        $this->programParticipationMailNotification = $programParticipationMailNotification;
    }

    public function send(Mailer $mailer, SenderInterface $sender, KonsultaMailMessage $mailMessage): void
    {
        $mailMessage->prependApiPath("/program-participations/{$this->programParticipation->getId()}");
        $this->clientMailNotification->send($mailer, $sender, $mailMessage);
    }

}
