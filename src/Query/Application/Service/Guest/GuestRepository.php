<?php

namespace Query\Application\Service\Guest;

use Query\Domain\Model\Guest;

class GuestRepository
{

    public function __construct()
    {
        
    }

    public function get(): Guest
    {
        return new Guest();
    }

}
