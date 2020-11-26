<?php

namespace Query\Domain\Model\Firm\Manager;

use Query\Domain\ {
    Model\Firm\Manager,
    SharedModel\Mail
};

class ManagerMail
{

    /**
     *
     * @var Manager
     */
    protected $manager;

    /**
     *
     * @var string
     */
    protected $id;

    /**
     *
     * @var Mail
     */
    protected $mail;

    public function __construct()
    {
        
    }

}
