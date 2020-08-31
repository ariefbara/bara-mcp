<?php

namespace Client\Domain\Model\Client;

use Client\Domain\Model\ProgramInterface;

class Participant
{

    /**
     *
     * @var string
     */
    protected $programId;

    /**
     *
     * @var string
     */
    protected $id;

    /**
     *
     * @var bool
     */
    protected $active;

    protected function __construct()
    {
        ;
    }
    
    public function isActiveParticipantOfProgram(ProgramInterface $program): bool
    {
        return $this->active && $this->programId === $program->getId();
    }

}
