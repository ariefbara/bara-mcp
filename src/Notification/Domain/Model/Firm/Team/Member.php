<?php

namespace Notification\Domain\Model\Firm\Team;

use Notification\Domain\Model\Firm\ {
    Client,
    Team
};

class Member
{
    /**
     *
     * @var Team
     */
    protected $team;
    /**
     *
     * @var string
     */
    protected $id;
    /**
     *
     * @var Client
     */
    protected $client;
}
