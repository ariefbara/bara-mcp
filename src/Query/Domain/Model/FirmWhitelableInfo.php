<?php

namespace Query\Domain\Model;

use Resources\ {
    ValidationRule,
    ValidationService
};

class FirmWhitelableInfo
{

    /**
     *
     * @var string|null
     */
    protected $url;

    /**
     *
     * @var string|null
     */
    protected $mailSenderAddress;

    /**
     *
     * @var string|null
     */
    protected $mailSenderName;

    public function getUrl(): ?string
    {
        return $this->url;
    }

    public function getMailSenderAddress(): ?string
    {
        return $this->mailSenderAddress;
    }

    public function getMailSenderName(): ?string
    {
        return $this->mailSenderName;
    }

    protected function setUrl(string $url): void
    {
        $errorDetail = 'bad request: invalid firm whitelable url format';
        ValidationService::build()
                ->addRule(ValidationRule::url())
                ->execute($url, $errorDetail);
        $this->url = $url;
    }

    protected function setMailSenderAddress(string $mailSenderAddress): void
    {
        $errorDetail = 'bad request: invalid firm whitelable mail sender address format';
        ValidationService::build()
                ->addRule(ValidationRule::email())
                ->execute($mailSenderAddress, $errorDetail);
        $this->mailSenderAddress = $mailSenderAddress;
    }

    protected function setMailSenderName(string $mailSenderName): void
    {
        $errorDetail = 'bad request: firm whitelable mail sender name is mandatory';
        ValidationService::build()
                ->addRule(ValidationRule::notEmpty())
                ->execute($mailSenderName, $errorDetail);
        $this->mailSenderName = $mailSenderName;
    }

    public function __construct(string $url, string $mailSenderAddress, string $mailSenderName)
    {
        $this->setUrl($url);
        $this->setMailSenderAddress($mailSenderAddress);
        $this->setMailSenderName($mailSenderName);
    }

}
