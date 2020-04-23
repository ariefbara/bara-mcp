<?php

namespace Firm\Domain\Model\Firm\Program;

use Doctrine\Common\Collections\ArrayCollection;
use Firm\Domain\Model\Firm\ {
    Program,
    WorksheetForm
};
use Resources\ {
    Exception\RegularException,
    ValidationRule,
    ValidationService
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
     * @var Mission
     */
    protected $previousMission = null;

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
     * @var string
     */
    protected $description = null;

    /**
     *
     * @var string
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
    protected $nextMissions = null;

    function getProgram(): Program
    {
        return $this->program;
    }

    function getPreviousMission(): ?Mission
    {
        return $this->previousMission;
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

    function getWorksheetForm(): WorksheetForm
    {
        return $this->worksheetForm;
    }

    /**
     * 
     * @return Mission[]
     */
    function getUnremovedNextMissions()
    {
        return $this->nextMissions->getIterator();
    }

    protected function setName(string $name): void
    {
        $errorDetail = "bad request: mission name is required";
        ValidationService::build()
                ->addRule(ValidationRule::notEmpty())
                ->execute($name, $errorDetail);
        $this->name = $name;
    }

    protected function __construct(
            Program $program, string $id, string $name, ?string $description, WorksheetForm $worksheetForm,
            ?string $position)
    {
        $this->program = $program;
        $this->id = $id;
        $this->setName($name);
        $this->description = $description;
        $this->position = $position;
        $this->worksheetForm = $worksheetForm;
        $this->published = false;
    }

    public static function createRoot(
            Program $program, string $id, string $name, ?string $description, WorksheetForm $worksheetForm,
            ?string $position): self
    {
        return new static($program, $id, $name, $description, $worksheetForm, $position);
    }

    public function createBranch(
            string $id, string $name, ?string $description, WorksheetForm $worksheetForm, ?string $position): self
    {
        $branch = new static($this->program, $id, $name, $description, $worksheetForm, $position);
        $branch->previousMission = $this;
        return $branch;
    }

    public function update(string $name, ?string $description, ?string $position): void
    {
        $this->setName($name);
        $this->description = $description;
        $this->position = $position;
    }

    public function publish(): void
    {
        $this->assertUnpublished();
        $this->published = true;
    }

    protected function assertUnpublished(): void
    {
        if ($this->published) {
            $errorDetail = "forbidden: request only valid for non published mission";
            throw RegularException::forbidden($errorDetail);
        }
    }

}
