<?php

namespace ActivityInvitee\Domain\DependencyModel\Firm\Client;

use ActivityInvitee\Domain\DependencyModel\Firm\Client;

class TeamMembership
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
     * @var string
     */
    protected $teamId;

    /**
     *
     * @var bool
     */
    protected $active;

    protected function __construct()
    {
        
    }

}
