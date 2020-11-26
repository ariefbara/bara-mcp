<?php

namespace Notification\Domain\Model\Firm;

use Notification\Domain\Model\Firm;

class Program
{
    /**
     *
     * @var Firm
     */
    protected $firm;
    /**
     *
     * @var string
     */
    protected $id;
    
    protected function __construct()
    {
    }
    
    public function getFirmDomain(): string
    {
        return $this->firm->getDomain();
    }
    
    public function getFirmLogoPath(): ?string
    {
        return $this->firm->getLogoPath();
    }
    
    public function getFirmMailSenderAddress(): string
    {
        return $this->firm->getMailSenderAddress();
    }
    
    public function getFirmMailSenderName(): string
    {
        return $this->firm->getMailSenderName();
    }
}
