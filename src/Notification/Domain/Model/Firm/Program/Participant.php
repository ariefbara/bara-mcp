<?php

namespace Notification\Domain\Model\Firm\Program;

use Notification\Domain\ {
    Model\Firm\Client\ClientProgramParticipation,
    Model\Firm\Program,
    Model\Firm\Team\Member,
    Model\Firm\Team\TeamProgramParticipation,
    Model\User\UserProgramParticipation,
    SharedModel\CanSendPersonalizeMail,
    SharedModel\ContainNotification
};
use SharedContext\Domain\ValueObject\MailMessage;

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
     * @var TeamProgramParticipation|null
     */
    protected $teamParticipant;

    /**
     *
     * @var ClientProgramParticipation|null
     */
    protected $clientParticipant;

    /**
     *
     * @var UserProgramParticipation|null
     */
    protected $userParticipant;

    protected function __construct()
    {
        
    }

    public function getName(): string
    {
        if (isset($this->teamParticipant)) {
            return $this->teamParticipant->getTeamName();
        }
        if (isset($this->clientParticipant)) {
            return $this->clientParticipant->getClientFullName();
        }
        if (isset($this->userParticipant)) {
            return $this->userParticipant->getUserFullName();
        }
    }

    public function getFirmDomain(): string
    {
        return $this->program->getFirmDomain();
    }
    
    public function getFirmLogoPath(): ?string
    {
        return $this->program->getFirmLogoPath();
    }

    public function getFirmMailSenderAddress(): string
    {
        return $this->program->getFirmMailSenderAddress();
    }

    public function getFirmMailSenderName(): string
    {
        return $this->program->getFirmMailSenderName();
    }

    public function registerMailRecipient(
            CanSendPersonalizeMail $mailGenerator, MailMessage $mailMessage, ?Member $excludedMember = null): void
    {
        if (isset($this->teamParticipant)) {
            $this->teamParticipant->registerTeamMembersAsMailRecipient($mailGenerator, $mailMessage, $excludedMember);
        }
        if (isset($this->clientParticipant)) {
            $this->clientParticipant->registerClientAsMailRecipient($mailGenerator, $mailMessage);
        }
        if (isset($this->userParticipant)) {
            $this->userParticipant->registerUserAsMailRecipient($mailGenerator, $mailMessage);
        }
    }

    public function registerNotificationRecipient(
            ContainNotification $notification, ?Member $excludedMember = null): void
    {
        if (isset($this->teamParticipant)) {
            $this->teamParticipant->registerTeamMembersAsNotificationRecipient($notification, $excludedMember);
        }
        if (isset($this->clientParticipant)) {
            $this->clientParticipant->registerClientAsNotificationRecipient($notification);
        }
        if (isset($this->userParticipant)) {
            $this->userParticipant->registerUserAsNotificationRecipient($notification);
        }
    }

}
