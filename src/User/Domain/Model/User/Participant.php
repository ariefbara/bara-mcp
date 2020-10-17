<?php

namespace User\Domain\Model\User;

use User\Domain\Model\ProgramInterface;

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
    }
    
    public function isActiveParticipantInProgram(ProgramInterface $program): bool
    {
        return $this->active && $this->programId === $program->getId();
    }

}
