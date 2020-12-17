<?php

namespace Notification\Domain\Model\Firm\Program\Participant;

use Notification\Domain\Model\Firm\Program\Mission;
use Notification\Domain\Model\Firm\Program\Participant;
use Notification\Domain\SharedModel\CanSendPersonalizeMail;
use Notification\Domain\SharedModel\ContainNotification;
use SharedContext\Domain\ValueObject\MailMessage;

class Worksheet
{

    /**
     *
     * @var Participant
     */
    protected $participant;

    /**
     *
     * @var string
     */
    protected $id;

    /**
     *
     * @var string
     */
    protected $name;

    /**
     *
     * @var Mission
     */
    protected $mission;

    public function getId(): string
    {
        return $this->id;
    }

    function getName(): string
    {
        return $this->name;
    }

    protected function __construct()
    {
        ;
    }

    public function getParticipantName(): string
    {
        return $this->participant->getName();
    }

    public function getFirmDomain(): string
    {
        return $this->participant->getFirmDomain();
    }

    public function getFirmLogoPath(): ?string
    {
        return $this->participant->getFirmLogoPath();
    }

    public function getFirmMailSenderAddress(): string
    {
        return $this->participant->getFirmMailSenderAddress();
    }

    public function getFirmMailSenderName(): string
    {
        return $this->participant->getFirmMailSenderName();
    }

    public function getMissionName(): string
    {
        return $this->mission->getName();
    }
    
    public function getParticipantId(): string
    {
        return $this->participant->getId();
    }

    public function registerParticipantAsMailRecipient(CanSendPersonalizeMail $mailGenerator, MailMessage $mailMessage): void
    {
        $mailMessage = $mailMessage->prependUrlPath("/worksheets/{$this->id}");
        $this->participant->registerMailRecipient($mailGenerator, $mailMessage);
    }

    public function registerParticipantAsNotificationRecipient(ContainNotification $notification): void
    {
        $this->participant->registerNotificationRecipient($notification);
    }

}
