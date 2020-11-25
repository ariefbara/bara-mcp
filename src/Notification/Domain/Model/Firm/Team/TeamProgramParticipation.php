<?php

namespace Notification\Domain\Model\Firm\Team;

use Notification\Domain\ {
    Model\Firm\Program\Participant,
    Model\Firm\Team,
    SharedModel\CanSendPersonalizeMail,
    SharedModel\ContainNotification
};
use SharedContext\Domain\ValueObject\MailMessage;

class TeamProgramParticipation
{

    /**
     *
     * @var Team
     */
    protected $team;

    /**
     *
     * @var string
     */
    protected $id;

    /**
     *
     * @var Participant
     */
    protected $programParticipation;

    protected function __construct()
    {
        
    }

    public function getTeamName(): string
    {
        return $this->team->getName();
    }

    public function registerTeamMembersAsMailRecipient(
            CanSendPersonalizeMail $mailGenerator, MailMessage $mailMessage, ?Member $excludedMember): void
    {
        $modifiedMailMessage = $mailMessage->prependUrlPath("/program-participations/{$this->id}");
        $this->team->registerAllActiveMembersAsMailRecipient($mailGenerator, $modifiedMailMessage, $excludedMember);
    }

    public function registerTeamMembersAsNotificationRecipient(
            ContainNotification $notification, ?Member $excludedMember): void
    {
        $this->team->registerAllActiveMembersAsNotificationRecipient($notification, $excludedMember);
    }

}
