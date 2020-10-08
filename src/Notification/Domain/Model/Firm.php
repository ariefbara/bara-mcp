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
     * @var FirmWhitelableInfo
     */
    protected $firmWhitelableInfo;
}
