<?php

namespace Notification\Domain\Model\Firm\Personnel;

use Notification\Domain\Model\ {
    Firm\Personnel,
    SharedEntity\KonsultaMailMessage
};
use Resources\Application\Service\ {
    Mailer,
    SenderInterface
};

class PersonnelMailNotification
{
    /**
     *
     * @var Personnel
     */
    protected $personnel;
    
    public function __construct(Personnel $personnel)
    {
        $this->personnel = $personnel;
    }
    
    public function send(Mailer $mailer, SenderInterface $sender, KonsultaMailMessage $mailMessage): void
    {
        $recipient = $this->personnel->getMailRecipient();
        $mailer->send($sender, $mailMessage, $recipient);
    }

    
}
