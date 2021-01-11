<?php

namespace SharedContext\Domain\ValueObject;

class MailMessage
{

    /**
     *
     * @var string
     */
    protected $subject;

    /**
     *
     * @var string|null
     */
    protected $logoPath;

    /**
     *
     * @var string
     */
    protected $greetings;

    /**
     *
     * @var array
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

    /**
     * 
     * @var bool
     */
    protected $showLink;

    public function getSubject(): string
    {
        return $this->subject;
    }

    function getLogoPath(): ?string
    {
        return $this->logoPath;
    }

    function getGreetings(): string
    {
        return $this->greetings;
    }

    function getMainMessage(): string
    {
        return $this->mainMessage;
    }

    function getShortcutLink(): string
    {
        return $this->domain . $this->urlPath;
    }

    public function __construct(
            string $subject, string $greetings, array $mainMessage, string $domain, string $urlPath, ?string $logoPath,
            ?bool $showLink = true)
    {
        $this->subject = $subject;
        $this->greetings = $greetings;
        $this->mainMessage = $mainMessage;
        $this->domain = $domain;
        $this->urlPath = $urlPath;
        $this->logoPath = $logoPath;
        $this->showLink = $showLink;
    }

    public function appendRecipientFirstNameInGreetings(string $recipientFirstName): self
    {
        $greetings = $this->greetings . " $recipientFirstName";
        return new static(
                $this->subject, $greetings, $this->mainMessage, $this->domain, $this->urlPath, $this->logoPath,
                $this->showLink);
    }

    public function prependUrlPath(string $urlPath): self
    {
        $urlPath = $urlPath . $this->urlPath;
        return new static(
                $this->subject, $this->greetings, $this->mainMessage, $this->domain, $urlPath, $this->logoPath,
                $this->showLink);
    }

    public function appendUrlPath(string $urlPath): self
    {
        $urlPath = $this->urlPath . $urlPath;
        return new static(
                $this->subject, $this->greetings, $this->mainMessage, $this->domain, $urlPath, $this->logoPath,
                $this->showLink);
    }

    public function getTextMessage(): string
    {
        $textMessage = "";
        foreach ($this->mainMessage as $message) {
            $textMessage .= $textMessage . "\n" .  $message;
        }
        if ($this->showLink) {
            return <<<_MESSAGE
{$this->greetings},

{$textMessage}

{$this->domain}{$this->urlPath}
_MESSAGE;
        } else {
            return <<<_MESSAGE
{$this->greetings},

{$textMessage}
_MESSAGE;
        }
    }

    public function getHtmlMessage(): string
    {
        $doc = new \DOMDocument();
        if ($this->showLink) {
            $doc->loadHTMLFile(dirname(__FILE__) . DIRECTORY_SEPARATOR . 'EmailTemplate.html',
                    LIBXML_NOWARNING | LIBXML_NOERROR);
            $doc->getElementById("shortcut_link")->setAttribute("href", $this->domain . $this->urlPath);
        } else {
            $doc->loadHTMLFile(dirname(__FILE__) . DIRECTORY_SEPARATOR . 'EmailTemplateWithoutLink.html',
                    LIBXML_NOWARNING | LIBXML_NOERROR);
        }
        $mainMessage = $doc->getElementById("main_message");
        foreach ($this->mainMessage as $message) {
            $mainMessage->appendChild($doc->createElement("p", $message));
        }

        $firmDomain = $doc->createTextNode($this->domain);
        $headerSubject = $doc->createTextNode($this->subject);
        $messageSubject = $doc->createTextNode($this->subject);
        $greetingText = $doc->createTextNode($this->greetings);
//        $logoPath = empty($this->logoPath)? null: "app.innov.id/bara-mcp/public/storage/app" . $this->logoPath;
//        $doc->getElementById("firm_logo")->setAttribute("src", urlencode($logoPath));
        $doc->getElementById("firm_domain")->appendChild($firmDomain);
        $doc->getElementById("header_subject")->appendChild($headerSubject);
        $doc->getElementById("message_subject")->appendChild($messageSubject);
        $doc->getElementById("greeting")->appendChild($greetingText);

        return $doc->saveHTML();
    }

}
