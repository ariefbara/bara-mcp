<?php

namespace Notification\Domain\Model\Firm\Program\Consultant;

use Notification\Domain\Model\ {
    Firm\Personnel\PersonnelMailNotification,
    Firm\Program\Consultant,
    SharedEntity\KonsultaMailMessage
};
use Resources\Application\Service\ {
    Mailer,
    SenderInterface
};

class ConsultantMailNotification
{

    /**
     *
     * @var Consultant
     */
    protected $consultant;

    /**
     *
     * @var PersonnelMailNotification
     */
    protected $personnelMailNotification;

    public function __construct(Consultant $consultant, PersonnelMailNotification $personnelMailNotification)
    {
        $this->consultant = $consultant;
        $this->personnelMailNotification = $personnelMailNotification;
    }

    public function send(Mailer $mailer, SenderInterface $sender, KonsultaMailMessage $mailMessage): void
    {
        $this->personnelMailNotification->send($mailer, $sender, $mailMessage);
    }

}
