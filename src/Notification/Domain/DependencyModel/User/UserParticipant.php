<?php

namespace Notification\Domain\Model\User;

use Notification\Domain\Model\ {
    Firm\Program\Participant,
    User
};


class UserParticipant
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
    
    public function getUserName(): string
    {
        return $this->user->getName();
    }

}
