<?php

namespace ActivityInvitee\Domain\Model;

use ActivityInvitee\Domain\DependencyModel\Firm\Personnel\Coordinator;

class CoordinatorInvitee
{

    /**
     *
     * @var Coordinator
     */
    protected $coordinator;

    /**
     *
     * @var string
     */
    protected $id;

    /**
     *
     * @var Invitee
     */
    protected $invitee;

    protected function __construct()
    {
        
    }
    
    public function submitReport(): void
    {
        
    }

}
