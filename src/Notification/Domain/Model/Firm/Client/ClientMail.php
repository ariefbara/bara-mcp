<?php

namespace Notification\Domain\Model\Firm\Client;

use Notification\Domain\{
    Model\Firm\Client,
    SharedModel\Mail
};

class ClientMail
{

    /**
     *
     * @var Client
     */
    protected $client;

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
            Client $client, string $id, string $senderMailAddress, string $senderName, string $subject, string $message,
            ?string $htmlMessage, string $recipientMailAddress, string $recipientName)
    {
        $this->client = $client;
        $this->id = $id;
        $this->mail = new Mail($id, $senderMailAddress, $senderName, $subject, $message, $htmlMessage,
                $recipientMailAddress, $recipientName);
    }

}
