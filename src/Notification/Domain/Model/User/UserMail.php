<?php

namespace Notification\Domain\Model\User;

use Notification\Domain\ {
    Model\User,
    SharedModel\Mail
};
use SharedContext\Domain\ValueObject\MailMessage;

class UserMail
{

    /**
     *
     * @var User
     */
    protected $user;

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

    public function __construct(
            User $user, string $id, string $senderMailAddress, string $senderName, MailMessage $mailMessage,
            string $recipientMailAddress, string $recipientName)
    {
        $this->user = $user;
        $this->id = $id;
        $this->mail = new Mail($id, $senderMailAddress, $senderName, $mailMessage, $recipientMailAddress, $recipientName);
    }

}
