<?php

namespace Notification\Domain\Model;

use Query\Domain\Model\FirmWhitelableInfo;

class Firm
{

    /**
     *
     * @var string
     */
    protected $id;

    /**
     *
     * @var string
     */
    protected $identifier;

    /**
     *
     * @var FirmWhitelableInfo
     */
    protected $firmWhitelableInfo;

    function getIdentifier(): string
    {
        return $this->identifier;
    }

    protected function __construct()
    {
        ;
    }

    public function getDomain(): string
    {
        return $this->firmWhitelableInfo->getUrl();
    }

    public function getMailSenderAddress(): string
    {
        return $this->firmWhitelableInfo->getMailSenderAddress();
    }

    public function getMailSenderName(): string
    {
        return $this->firmWhitelableInfo->getMailSenderName();
    }

}
