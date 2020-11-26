<?php

namespace Notification\Domain\Model\Firm\Personnel;

use Notification\Domain\{
    Model\Firm\Personnel,
    SharedModel\Mail
};
use SharedContext\Domain\ValueObject\MailMessage;

class PersonnelMail
{

    /**
     *
     * @var Personnel
     */
    protected $personnel;

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
            Personnel $personnel, string $id, string $senderMailAddress, string $senderName, MailMessage $mailMessage,
            string $recipientMailAddress, string $recipientName)
    {
        $this->personnel = $personnel;
        $this->id = $id;
        $this->mail = new Mail($id, $senderMailAddress, $senderName, $mailMessage, $recipientMailAddress, $recipientName);
    }

}
