<?php

namespace Query\Domain\Model\User;

use Query\Domain\{
    Model\User,
    SharedModel\Mail
};

class UserMail
{

    /**
     *
     * @var User
     */
    protected $user;

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

    protected function __construct()
    {
        
    }

}
