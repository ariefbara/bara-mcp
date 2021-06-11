<?php

namespace Query\Domain\Model;

use Query\Domain\Model\Firm\BioSearchFilter;
use Query\Domain\Model\Firm\FirmFileInfo;

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
     * @var FirmFileInfo|null
     */
    protected $logo;

    /**
     *
     * @var string|null
     */
    protected $displaySetting;

    /**
     *
     * @var bool
     */
    protected $suspended = false;

    /**
     * 
     * @var BioSearchFilter|null
     */
    protected $bioSearchFilter;

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

    public function getDisplaySetting(): ?string
    {
        return $this->displaySetting;
    }

    function getLogo(): ?FirmFileInfo
    {
        return $this->logo;
    }

    function isSuspended(): bool
    {
        return $this->suspended;
    }

    public function getBioSearchFilter(): ?BioSearchFilter
    {
        return $this->bioSearchFilter;
    }

    protected function __construct()
    {
        
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
