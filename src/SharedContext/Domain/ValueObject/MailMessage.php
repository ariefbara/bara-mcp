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

    public function __construct(string $subject, string $greetings, string $mainMessage, string $domain, string $urlPath, ?string $logoPath)
    {
        $this->subject = $subject;
        $this->greetings = $greetings;
        $this->mainMessage = $mainMessage;
        $this->domain = $domain;
        $this->urlPath = $urlPath;
        $this->logoPath = $logoPath;
    }

    public function appendRecipientFirstNameInGreetings(string $recipientFirstName): self
    {
        $greetings = $this->greetings . " $recipientFirstName";
        return new static($this->subject, $greetings, $this->mainMessage, $this->domain, $this->urlPath, $this->logoPath);
    }

    public function prependUrlPath(string $urlPath): self
    {
        $urlPath = $urlPath . $this->urlPath;
        return new static($this->subject, $this->greetings, $this->mainMessage, $this->domain, $urlPath, $this->logoPath);
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
        $doc = new \DOMDocument();
        $doc->loadHTMLFile(dirname(__FILE__) . DIRECTORY_SEPARATOR . 'HtmlMailTemplate.html',
                LIBXML_NOWARNING | LIBXML_NOERROR);
        
        $greetingText = $doc->createTextNode($this->greetings);
        $mainMessageText = $doc->createTextNode($this->mainMessage);
//        $logoPath = empty($this->logoPath)? null: "app.innov.id/bara-mcp/public/storage/app" . $this->logoPath;
//        $doc->getElementById("firm_logo")->setAttribute("src", urlencode($logoPath));
        $doc->getElementById("greeting")->appendChild($greetingText);
        $doc->getElementById("main_message")->appendChild($mainMessageText);
        $doc->getElementById("shortcut_link")->setAttribute("href", $this->domain . $this->urlPath);

        return $doc->saveHTML();
    }

}
