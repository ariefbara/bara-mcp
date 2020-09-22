<?php

namespace Notification\Domain\Model\Firm;

use Resources\Application\Service\SenderInterface;

class FirmMailSender implements SenderInterface
{

    /**
     *
     * @var string
     */
    protected $mailAddress;

    /**
     *
     * @var string
     */
    protected $name;

    public function __construct(string $mailAddress, string $name)
    {
        $this->mailAddress = $mailAddress;
        $this->name = $name;
    }

    public function getMailAddress(): string
    {
        return $this->mailAddress;
    }

    public function getName(): string
    {
        return $this->name;
    }

}
