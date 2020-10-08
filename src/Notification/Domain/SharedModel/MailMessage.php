<?php

namespace Notification\Domain\SharedModel;

class MailMessage
{

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
    protected $domain;

    /**
     *
     * @var string
     */
    protected $urlPath;

    public function getSubject(): string
    {
        return $this->subject;
    }

    public function __construct(string $subject, string $greetings, string $mainMessage, string $domain, string $urlPath)
    {
        $this->subject = $subject;
        $this->greetings = $greetings;
        $this->mainMessage = $mainMessage;
        $this->domain = $domain;
        $this->urlPath = $urlPath;
    }

    public function appendRecipientFirstNameInGreetings(string $recipientFirstName): self
    {
        $greetings = $this->greetings . " $recipientFirstName";
        return new static($this->subject, $greetings, $this->mainMessage, $this->domain, $this->urlPath);
    }

    public function prependUrlPath(string $urlPath): self
    {
        $urlPath = $urlPath . $this->urlPath;
        return new static($this->subject, $this->greetings, $this->mainMessage, $this->domain, $urlPath);
    }

    public function getTextMessage(): string
    {
        return <<<_MESSAGE
{$this->greetings},

{$this->mainMessage}

{$this->domain}{$this->urlPath}
_MESSAGE;
    }

    public function getHtmlMessage(): string
    {
        return <<<_MESSAGE
<p>{$this->greetings},</p>

<p>{$this->mainMessage}</p>

<p>{$this->domain}{$this->urlPath}</p>
_MESSAGE;
    }

}
