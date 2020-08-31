<?php

namespace Client\Domain\Model\Client;

use Client\Domain\Model\ {
    Client,
    ProgramInterface
};

class ProgramParticipation
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

    public function isActiveParticipantOfProgram(ProgramInterface $program): bool
    {
        return $this->participant->isActiveParticipantOfProgram($program);
    }

}
