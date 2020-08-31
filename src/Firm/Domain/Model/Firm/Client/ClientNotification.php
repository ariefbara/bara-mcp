<?php

namespace Firm\Domain\Model\Firm\Client;

use Firm\Domain\Model\{
    Firm\Client,
    SharedEntity\Notification
};

class ClientNotification
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
     * @var Notification
     */
    protected $notification;
    
    public function __construct(Client $client, string $id, string $message)
    {
//        $this->client = $client;
//        $this->id = $id;
//        $this->notification = $notification;
    }


}
