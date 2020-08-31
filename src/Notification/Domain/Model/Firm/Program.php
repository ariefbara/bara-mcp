<?php

namespace Notification\Domain\Model\Firm;

use Notification\Domain\Model\Firm;
use Query\Domain\Model\FirmWhitelableInfo;

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

    public function getId(): string
    {
        return $this->id;
    }

    protected function __construct()
    {
        ;
    }

    public function getFirmWhitelableInfo(): FirmWhitelableInfo
    {
        return $this->firm->getFirmWhitelableInfo();
    }

}
