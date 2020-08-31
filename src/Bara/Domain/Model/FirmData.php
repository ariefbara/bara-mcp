<?php

namespace Bara\Domain\Model;

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

    function __construct(
            ?string $name, ?string $identifier, ?string $whitelableUrl, ?string $whitelableMailSenderAddress,
            ?string $whitelableMailSenderName)
    {
        $this->name = $name;
        $this->identifier = $identifier;
        $this->whitelableUrl = $whitelableUrl;
        $this->whitelableMailSenderAddress = $whitelableMailSenderAddress;
        $this->whitelableMailSenderName = $whitelableMailSenderName;
    }

}
