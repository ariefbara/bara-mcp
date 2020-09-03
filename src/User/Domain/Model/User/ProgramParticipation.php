<?php

namespace User\Domain\Model\User;

use User\Domain\Model\ {
    ProgramInterface,
    User
};

class ProgramParticipation
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
     * @var Participant
     */
    protected $participant;
    
    protected function __construct()
    {
        ;
    }
    
    public function isActiveParticipantInProgram(ProgramInterface $program): bool
    {
        return $this->participant->isActiveParticipantInProgram($program);
    }

}
