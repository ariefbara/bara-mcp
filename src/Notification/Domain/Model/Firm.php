<?php

namespace Notification\Domain\Model;

use Notification\Domain\Model\Firm\FirmMailSender;
use Query\Domain\Model\FirmWhitelableInfo;
use Resources\Application\Service\SenderInterface;

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
     * @var FirmWhitelableInfo
     */
    protected $firmWhitelableInfo;

    /**
     *
     * @var bool
     */
    protected $suspended = false;

    protected function __construct()
    {
        ;
    }
    
    public function getWhitelableUrl(): string
    {
        return $this->firmWhitelableInfo->getUrl();
    }
    
    public function getMailSender(): SenderInterface
    {
        $mailAddress = $this->firmWhitelableInfo->getMailSenderAddress();
        $name = $this->firmWhitelableInfo->getMailSenderName();
        
        return new FirmMailSender($mailAddress, $name);
    }

}
