<?php

namespace Query\Domain\Model\Firm\Personnel;

use Query\Domain\{
    Model\Firm\Personnel,
    SharedModel\Mail
};

class PersonnelMail
{

    /**
     *
     * @var Personnel
     */
    protected $personnel;

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
