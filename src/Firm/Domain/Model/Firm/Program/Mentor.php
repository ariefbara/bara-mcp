<?php

namespace Firm\Domain\Model\Firm\Program;

use Firm\Domain\Model\Firm\{
    Personnel,
    Program
};

class Mentor
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
     * @var Personnel
     */
    protected $personnel;

    /**
     *
     * @var bool
     */
    protected $removed;

    function getProgram(): Program
    {
        return $this->program;
    }

    function getId(): string
    {
        return $this->id;
    }

    function getPersonnel(): Personnel
    {
        return $this->personnel;
    }

    function isRemoved(): bool
    {
        return $this->removed;
    }

    function __construct(Program $program, string $id, Personnel $personnel)
    {
        $this->program = $program;
        $this->id = $id;
        $this->personnel = $personnel;
        $this->removed = false;
    }

    public function reassign(): void
    {
        $this->removed = false;
    }

    public function remove(): void
    {
        $this->removed = true;
    }

}
