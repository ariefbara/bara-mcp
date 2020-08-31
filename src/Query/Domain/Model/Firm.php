<?php

namespace Query\Domain\Model;

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
    protected $name;

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

    /**
     *
     * @var bool
     */
    protected $suspended = false;

    function getId(): string
    {
        return $this->id;
    }

    function getName(): string
    {
        return $this->name;
    }

    function getIdentifier(): string
    {
        return $this->identifier;
    }

    function isSuspended(): bool
    {
        return $this->suspended;
    }

    protected function __construct()
    {
        ;
    }

    public function getWhitelableUrl(): string
    {
        return $this->firmWhitelableInfo->getUrl();
    }

    public function getWhitelableMailSenderAddress(): string
    {
        return $this->firmWhitelableInfo->getMailSenderAddress();
    }

    public function getWhitelableMailSenderName(): string
    {
        return $this->firmWhitelableInfo->getMailSenderName();
    }

}
