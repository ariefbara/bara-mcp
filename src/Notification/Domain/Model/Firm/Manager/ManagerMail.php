<?php

namespace Notification\Domain\Model\Firm\Manager;

use Notification\Domain\{
    Model\Firm\Manager,
    SharedModel\Mail
};
use SharedContext\Domain\ValueObject\MailMessage;

class ManagerMail
{

    /**
     *
     * @var Manager
     */
    protected $manager;

    /**
     *
     * @var string
     */
    protected $id;

    /**
     *
     * @var Mail
     */
    protected $mail;

    function __construct(
            Manager $manager, string $id, string $senderMailAddress, string $senderName, MailMessage $mailMessage,
            string $recipientMailAddress, string $recipientName)
    {
        $this->manager = $manager;
        $this->id = $id;
        $this->mail = new Mail($id, $senderMailAddress, $senderName, $mailMessage, $recipientMailAddress, $recipientName);
    }

}
