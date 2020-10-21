<?php

namespace Notification\Domain\Model\User;

use Notification\Domain\ {
    Model\User,
    SharedModel\Mail
};

class UserMail
{

    /**
     *
     * @var User
     */
    protected $user;

    /**
     *
     * @var id
     */
    protected $id;

    /**
     *
     * @var Mail
     */
    protected $mail;

    public function __construct(
            User $user, string $id, string $senderMailAddress, string $senderName, string $subject, string $message,
            ?string $htmlMessage, string $recipientMailAddress, string $recipientName)
    {
        $this->user = $user;
        $this->id = $id;
        $this->mail = new Mail($id, $senderMailAddress, $senderName, $subject, $message, $htmlMessage,
                $recipientMailAddress, $recipientName);
    }

}
