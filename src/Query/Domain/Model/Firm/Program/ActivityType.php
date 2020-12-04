<?php

namespace Query\Domain\Model\Firm\Program;

use Doctrine\Common\Collections\ {
    ArrayCollection,
    Criteria
};
use Query\Domain\Model\Firm\ {
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
     * @var bool
     */
    protected $disabled;

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

    function isDisabled(): bool
    {
        return $this->disabled;
    }

    protected function __construct()
    {
        
    }

    /**
     * 
     * @return ActivityParticipant[]
     */
    public function iterateParticipants(?bool $enableOnly = true)
    {
        if ($enableOnly) {
            $criteria = Criteria::create()
                    ->andWhere(Criteria::expr()->eq("disabled", false));
            return $this->participants->matching($criteria)->getIterator();
        } else {
            return $this->participants->getIterator();
        }
    }

}
