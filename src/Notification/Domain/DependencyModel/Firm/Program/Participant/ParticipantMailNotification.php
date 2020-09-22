<?php

namespace Notification\Domain\Model\Firm\Program\Participant;

use Notification\Domain\Model\{
    Firm\Client\ClientProgramParticipation\ClientProgramParticipationMailNotification,
    SharedEntity\KonsultaMailMessage
};
use Resources\Application\Service\{
    Mailer,
    SenderInterface
};
use User\Domain\Model\User\Participant;

class ParticipantMailNotification
{

    /**
     *
     * @var Participant
     */
    protected $participant;

    /**
     *
     * @var ClientProgramParticipationMailNotification
     */
    protected $clientParticipantMailNotification;

    /**
     *
     * @var UserProgramParticipationMailNotification
     */
    protected $userParticipantMailNotification;

    public function __construct(Participant $participant)
    {
        $this->participant = $participant;
    }

    public function send(Mailer $mailer, SenderInterface $sender, KonsultaMailMessage $mailMessage): void
    {
        $this->clientParticipantMailNotification->send($mailer, $sender, $mailMessage);
    }

}
