<?php

namespace ActivityInvitee\Domain\DependencyModel\Firm\Program;

use Doctrine\Common\Collections\ArrayCollection;

class ActivityType
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
     * @var ArrayCollection
     */
    protected $participants;

    protected function __construct()
    {
        
    }

}
