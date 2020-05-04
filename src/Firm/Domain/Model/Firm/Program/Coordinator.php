<?php

namespace Firm\Domain\Model\Firm\Program;

use Firm\Domain\Model\Firm\{
    Personnel,
    Program
};

class Coordinator
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

    function getPersonnel(): Personnel
    {
        return $this->personnel;
    }

    function getId(): string
    {
        return $this->id;
    }

    function isRemoved(): bool
    {
        return $this->removed;
    }

    function __construct(Program $program, $id, Personnel $personnel)
    {
        $this->program = $program;
        $this->id = $id;
        $this->personnel = $personnel;
        $this->removed = false;
    }

    public function remove(): void
    {
        $this->removed = true;
    }

    public function reassign(): void
    {
        $this->removed = false;
    }

}
