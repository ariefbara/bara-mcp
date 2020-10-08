<?php

namespace Notification\Domain\Model\Firm\Program;

use Notification\Domain\{
    Model\Firm\Program,
    Model\Firm\Team\Member,
    Model\Firm\Team\TeamProgramParticipation,
    SharedModel\canSendPersonalizeMail,
    SharedModel\MailMessage,
    SharedModel\Notification
};

class Participant
{

    /**
     *
     * @var Program
     */
    protected $program;

    /**
     *
     * @var string
     */
    protected $id;

    /**
     *
     * @var TeamProgramParticipation
     */
    protected $teamParticipant;
    protected $clientParticipant;
    protected $userParticipant;

    protected function __construct()
    {
        ;
    }

    public function getName(): string
    {
        
    }

    public function getFirmDomain(): string
    {
        
    }

    public function getFirmMailSenderAddress(): string
    {
        
    }

    public function getFirmMailSenderName(): string
    {
        
    }

    public function registerMailRecipient(
            canSendPersonalizeMail $mailGenerator, MailMessage $mailMessage, ?Member $excludedMember): void
    {
        
    }

    public function addNotificationRecipient(Notification $notification, ?Member $excludedMember): void
    {
        
    }

}
