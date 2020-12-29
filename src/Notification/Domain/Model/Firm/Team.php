<?php

namespace Notification\Domain\Model\Firm;

use Doctrine\Common\Collections\ArrayCollection;
use Notification\Domain\ {
    Model\Firm,
    Model\Firm\Team\Member,
    SharedModel\CanSendPersonalizeMail,
    SharedModel\ContainNotification
};
use SharedContext\Domain\ValueObject\MailMessage;

class Team
{

    /**
     *
     * @var Firm
     */
    protected $firm;

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
     * @var ArrayCollection
     */
    protected $members;

    public function getId(): string
    {
        return $this->id;
    }

    protected function __construct()
    {
        ;
    }

    public function getName(): string
    {
        return $this->name;
    }

    public function registerAllActiveMembersAsMailRecipient(
            CanSendPersonalizeMail $mailGenerator, MailMessage $mailMessage, ?Member $excludedMember): void
    {
        $modifiedMail = $mailMessage->prependUrlPath("/team/{$this->id}");
        foreach ($this->iterateActiveMembersNotEquals($excludedMember) as $member) {
            $member->registerClientAsMailRecipient($mailGenerator, $modifiedMail);
        }
    }

    public function registerAllActiveMembersAsNotificationRecipient(ContainNotification $notification,
            ?Member $excludedMember): void
    {
        foreach ($this->iterateActiveMembersNotEquals($excludedMember) as $member) {
            $member->registerClientAsNotificationRecipient($notification);
        }
    }

    /**
     * 
     * @param Member $excludedMember
     * @return Member[]
     */
    protected function iterateActiveMembersNotEquals(?Member $excludedMember)
    {
        $p = function (Member $member) use ($excludedMember) {
            return $member->isActiveMemberNotEqualsTo($excludedMember);
        };
        return $this->members->filter($p)->getIterator();
    }

}
