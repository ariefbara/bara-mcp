<?php

namespace Notifier\Domain\SharedModel;

use Doctrine\Common\Collections\ArrayCollection;
use Resources\Application\Service\MailInterface;

class PersonalizeMail implements MailInterface
{

    /**
     *
     * @var string
     */
    protected $senderMailAddress;

    /**
     *
     * @var string
     */
    protected $senderName;

    /**
     *
     * @var string
     */
    protected $subject;

    /**
     *
     * @var string
     */
    protected $greetings;

    /**
     *
     * @var string
     */
    protected $mainMessage;

    /**
     *
     * @var string
     */
    protected $hostUrl;

    /**
     *
     * @var string
     */
    protected $apiPath;

    /**
     *
     * @var ArrayCollection
     */
    protected $recipients;

    /**
     *
     * @var ArrayCollection
     */
    protected $dynamicAttachments;

    public function getAlternativeBody(): ?string
    {
        return null;
    }

    public function getBody(): string
    {
        $clickablePath = $this->url . $this->apiPath;
        return <<<_MESSAGE
{$this->greetings}

{$this->mainMessage}
$clickablePath;
_MESSAGE;
    }

    public function getDynamicAttachments()
    {
        return [];
    }

    public function getRecipients()
    {
        return $this->recipients->getIterator();
    }

    public function getSenderMailAddress(): string
    {
        return $this->senderMailAddress;
    }

    public function getSenderName(): string
    {
        return $this->senderName;
        
    }

    public function getSubject(): string
    {
        return $this->subject;
    }

}
