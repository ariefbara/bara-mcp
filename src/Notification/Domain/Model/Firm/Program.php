<?php

namespace Notification\Domain\Model\Firm;

use Notification\Domain\Model\Firm;
use Resources\Application\Service\SenderInterface;

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

    /**
     *
     * @var string
     */
    protected $name;

    /**
     *
     * @var bool
     */
    protected $removed = false;

    protected function __construct()
    {
        ;
    }
    
    public function getFirmWhitelableUrl(): string
    {
        return $this->firm->getWhitelableUrl();
    }
    
    public function getFirmMailSender(): SenderInterface
    {
        return $this->firm->getMailSender();
    }

}
