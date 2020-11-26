<?php

namespace Query\Domain\Model\Firm\Client;

use Query\Domain\{
    Model\Firm\Client,
    SharedModel\Mail
};

class ClientMail
{

    /**
     *
     * @var Client
     */
    protected $client;

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
