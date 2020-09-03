<?php

namespace Notification\Domain\Model\Firm\Program\ConsultationSetup\ConsultationRequest;

use Notification\ {
    Application\Service\MailNotificationInterface,
    Domain\Model\Firm\Program\ConsultationSetup\ConsultationRequest,
    Domain\Model\Firm\Program\Participant\ParticipantMailNotification,
    Domain\Model\SharedEntity\KonsultaMailMessage
};
use Resources\Application\Service\Mailer;

class ConsultationRequestMailNotificationForParticipant implements MailNotificationInterface
{

    /**
     *
     * @var ConsultationRequest
     */
    protected $consultationRequest;

    /**
     *
     * @var ParticipantMailNotification
     */
    protected $participantMailNotification;
    
    /**
     *
     * @var KonsultaMailMessage
     */
    protected $mailMessage;

    public function __construct(ConsultationRequest $consultationRequest,
            ParticipantMailNotification $participantMailNotification, KonsultaMailMessage $mailMessage)
    {
        $this->consultationRequest = $consultationRequest;
        $this->participantMailNotification = $participantMailNotification;
        $this->mailMessage = $mailMessage;
    }

    public function send(Mailer $mailer): void
    {
        $sender = $this->consultationRequest->getFirmMailSender();
        $this->participantMailNotification->send($mailer, $sender, $this->mailMessage);
    }


}
