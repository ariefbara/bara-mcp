<?php

namespace Client\Domain\Model\Client;

use Client\Domain\DependencyModel\Firm\Program;
use Client\Domain\DependencyModel\Firm\Program\Registrant;
use Client\Domain\Model\Client;
use Config\EventList;
use Resources\Domain\Event\CommonEvent;
use Resources\Domain\Model\EntityContainEvents;

class ClientRegistrant extends EntityContainEvents
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
     * @var Registrant
     */
    protected $registrant;

    public function __construct(Client $client, string $id, Registrant $registrant)
    {
        $this->client = $client;
        $this->id = $id;
        $this->registrant = $registrant;
        
        $event = new CommonEvent(EventList::CLIENT_REGISTRANT_CREATED, $this->id);
        $this->recordEvent($event);
    }

    public function isActiveRegistrationCorrespondWithProgram(Program $program): bool
    {
        return $this->registrant->isActiveRegistrationCorrespondWithProgram($program);
    }

}
