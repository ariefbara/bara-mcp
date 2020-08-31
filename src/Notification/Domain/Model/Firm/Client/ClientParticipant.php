<?php

namespace Notification\Domain\Model\Firm\Client;

use Notification\Domain\Model\Firm\Client;
use User\Domain\Model\User\Participant;

class ClientParticipant
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
     * @var Participant
     */
    protected $participant;

    protected function __construct()
    {
        ;
    }
    
    public function getClientName(): string
    {
        return $this->client->getName();
    }

}
