<?php

namespace Query\Domain\Model;

use Config\BaseConfig;
use Resources\ValidationRule;
use Resources\ValidationService;

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

    public function getUrl(): string
    {
        return isset($this->url)? $this->url: BaseConfig::KONSULTA_MAIN_URL;
    }

    public function getMailSenderAddress(): string
    {
        return isset($this->mailSenderAddress)? $this->mailSenderAddress: BaseConfig::MAIL_SENDER_ADDRESS;
    }

    public function getMailSenderName(): string
    {
        return isset($this->mailSenderName)? $this->mailSenderName: BaseConfig::MAIL_SENDER_NAME;
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
