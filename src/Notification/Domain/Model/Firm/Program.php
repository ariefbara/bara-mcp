<?php

namespace Notification\Domain\Model\Firm;

use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Criteria;
use Notification\Domain\Model\Firm;
use Notification\Domain\SharedModel\CanSendPersonalizeMail;
use Notification\Domain\SharedModel\ContainNotificationforCoordinator;
use SharedContext\Domain\ValueObject\MailMessage;

class Program
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
     * @var ArrayCollection
     */
    protected $coordinators;

    function getId(): string
    {
        return $this->id;
    }

    protected function __construct()
    {
        
    }

    public function getFirmDomain(): string
    {
        return $this->firm->getDomain();
    }

    public function getFirmLogoPath(): ?string
    {
        return $this->firm->getLogoPath();
    }

    public function getFirmMailSenderAddress(): string
    {
        return $this->firm->getMailSenderAddress();
    }

    public function getFirmMailSenderName(): string
    {
        return $this->firm->getMailSenderName();
    }

    public function registerAllCoordinatorsAsMailRecipient(
            CanSendPersonalizeMail $mailGenerator, MailMessage $mailMessage): void
    {
        $mailMessage = $mailMessage->prependUrlPath("/program-coordinator/{$this->id}");
        $criteria = Criteria::create()
                ->andWhere(Criteria::expr()->eq("active", true));
        foreach ($this->coordinators->matching($criteria)->getIterator() as $coordinator) {
            $coordinator->registerAsMailRecipient($mailGenerator, $mailMessage, $haltPrependUrlPath = true);
        }
    }

    public function registerAllCoordiantorsAsNotificationRecipient(ContainNotificationforCoordinator $notification): void
    {
        $criteria = Criteria::create()
                ->andWhere(Criteria::expr()->eq("active", true));
        foreach ($this->coordinators->matching($criteria)->getIterator() as $coordinator) {
            $notification->addCoordinatorAsRecipient($coordinator);
        }
    }

}
