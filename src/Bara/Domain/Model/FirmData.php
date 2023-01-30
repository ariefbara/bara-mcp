<?php

namespace Bara\Domain\Model;

use Bara\Domain\Model\Firm\ManagerData;

class FirmData
{

    /**
     *
     * @var string||null
     */
    protected $name;

    /**
     *
     * @var string||null
     */
    protected $identifier;

    /**
     *
     * @var string||null
     */
    protected $whitelableUrl;

    /**
     *
     * @var string||null
     */
    protected $whitelableMailSenderAddress;

    /**
     *
     * @var string||null
     */
    protected $whitelableMailSenderName;

    /**
     * 
     * @var float||null
     */
    protected $sharingPercentage;

    /**
     * 
     * @var ManagerData[]
     */
    protected $listOfManagerData = [];

    function getName(): ?string
    {
        return $this->name;
    }

    function getIdentifier(): ?string
    {
        return $this->identifier;
    }

    function getWhitelableUrl(): ?string
    {
        return $this->whitelableUrl;
    }

    function getWhitelableMailSenderAddress(): ?string
    {
        return $this->whitelableMailSenderAddress;
    }

    function getWhitelableMailSenderName(): ?string
    {
        return $this->whitelableMailSenderName;
    }

    public function getSharingPercentage(): ?float
    {
        return $this->sharingPercentage;
    }

    public function getListOfManagerData(): array
    {
        return $this->listOfManagerData;
    }

    public function __construct(
            ?string $name, ?string $identifier, ?string $whitelableUrl, ?string $whitelableMailSenderAddress,
            ?string $whitelableMailSenderName, ?float $sharingPercentage)
    {
        $this->name = $name;
        $this->identifier = $identifier;
        $this->whitelableUrl = $whitelableUrl;
        $this->whitelableMailSenderAddress = $whitelableMailSenderAddress;
        $this->whitelableMailSenderName = $whitelableMailSenderName;
        $this->sharingPercentage = $sharingPercentage;
    }

//    function __construct(
//            ?string $name, ?string $identifier, ?string $whitelableUrl, ?string $whitelableMailSenderAddress,
//            ?string $whitelableMailSenderName)
//    {
//        $this->name = $name;
//        $this->identifier = $identifier;
//        $this->whitelableUrl = $whitelableUrl;
//        $this->whitelableMailSenderAddress = $whitelableMailSenderAddress;
//        $this->whitelableMailSenderName = $whitelableMailSenderName;
//    }

    public function addManager(ManagerData $managerData): self
    {
        $this->listOfManagerData[] = $managerData;
        return $this;
    }

}
