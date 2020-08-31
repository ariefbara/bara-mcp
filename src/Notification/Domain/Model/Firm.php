<?php

namespace Notification\Domain\Model;

use Query\Domain\Model\FirmWhitelableInfo;

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

    public function getFirmWhitelableInfo(): FirmWhitelableInfo
    {
        return $this->firmWhitelableInfo;
    }

    protected function __construct()
    {
        ;
    }

}
