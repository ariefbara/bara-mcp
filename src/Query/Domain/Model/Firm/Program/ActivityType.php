<?php

namespace Query\Domain\Model\Firm\Program;

use Doctrine\Common\Collections\ArrayCollection;
use Query\Domain\Model\Firm\{
    Program,
    Program\ActivityType\ActivityParticipant
};

class ActivityType
{

    /**
     *
     * @var Program
     */
    protected $program;

    /**
     *
     * @var string
     */
    protected $id;

    /**
     *
     * @var string
     */
    protected $name;

    /**
     *
     * @var string|null
     */
    protected $description;

    /**
     *
     * @var ArrayCollection
     */
    protected $participants;

    function getProgram(): Program
    {
        return $this->program;
    }

    function getId(): string
    {
        return $this->id;
    }

    function getName(): string
    {
        return $this->name;
    }

    function getDescription(): ?string
    {
        return $this->description;
    }

    protected function __construct()
    {
        
    }

    /**
     * 
     * @return ActivityParticipant[]
     */
    public function iterateParticipants()
    {
        return $this->participants->getIterator();
    }

}
