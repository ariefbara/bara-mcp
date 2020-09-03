<?php

namespace Notification\Domain\Model\Firm;

class FirmWhitelableMailInfo
{

    /**
     *
     * @var string
     */
    protected $senderMailAddress;

    /**
     *
     * @var string
     */
    protected $senderName;

    /**
     *
     * @var string
     */
    protected $urlLink;

    public function getSenderMailAddress(): string
    {
        return $this->senderMailAddress;
    }

    public function getSenderName(): string
    {
        return $this->senderName;
    }

    public function getUrlLink(): string
    {
        return $this->urlLink;
    }

    public function __construct(string $senderMailAddress, string $senderName, string $urlLink)
    {
        $this->senderMailAddress = $senderMailAddress;
        $this->senderName = $senderName;
        $this->urlLink = $urlLink;
    }

}
