<?php

namespace Notification\Domain\Model\Firm\Program\ConsultationSetup\ConsultationRequest;

use Notification\ {
    Application\Service\MailNotificationInterface,
    Domain\Model\Firm\Program\Consultant\ConsultantMailNotification,
    Domain\Model\Firm\Program\ConsultationSetup\ConsultationRequest,
    Domain\Model\SharedEntity\KonsultaMailMessage
};
use Resources\Application\Service\Mailer;

class ConsultationRequestMailNotificationForConsultant implements MailNotificationInterface
{

    /**
     *
     * @var ConsultationRequest
     */
    protected $consultationRequest;

    /**
     *
     * @var ConsultantMailNotification
     */
    protected $consultantMailNotification;

    /**
     *
     * @var KonsultaMailMessage
     */
    protected $mailMessage;

    public function __construct(
            ConsultationRequest $consultationRequest, ConsultantMailNotification $consultantMailNotification,
            KonsultaMailMessage $mailMessage)
    {
        $this->consultationRequest = $consultationRequest;
        $this->consultantMailNotification = $consultantMailNotification;
        $this->mailMessage = $mailMessage;
    }

    public function send(Mailer $mailer): void
    {
        $sender = $this->consultationRequest->getFirmMailSender();
        $this->consultantMailNotification->send($mailer, $sender, $this->mailMessage);
    }

}
