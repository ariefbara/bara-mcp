<?php

namespace Query\Domain\Model\Firm\Program;

use Doctrine\Common\Collections\ {
    ArrayCollection,
    Criteria
};
use Query\Domain\Model\Firm\ {
    Program,
    WorksheetForm
};

class Mission
{

    /**
     *
     * @var Program
     */
    protected $program;

    /**
     *
     * @var Mission||null
     */
    protected $parent = null;

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
     * @var string||null
     */
    protected $description = null;

    /**
     *
     * @var string||null
     */
    protected $position = null;

    /**
     *
     * @var bool
     */
    protected $published = false;

    /**
     *
     * @var WorksheetForm
     */
    protected $worksheetForm;

    /**
     *
     * @var ArrayCollection
     */
    protected $branches;

    function getProgram(): Program
    {
        return $this->program;
    }

    function getParent(): ?Mission
    {
        return $this->parent;
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

    function getPosition(): ?string
    {
        return $this->position;
    }

    function isPublished(): bool
    {
        return $this->published;
    }

    function getWorksheetForm(): ?WorksheetForm
    {
        return $this->worksheetForm;
    }

    /**
     * 
     * @return Mission[]
     */
    function getUnremovedBranches()
    {
        $criteria = Criteria::create()
                ->andWhere(Criteria::expr()->eq('removed', false));
        return $this->branches->matching($criteria)->getIterator();
    }

    protected function __construct()
    {
        ;
    }

}
