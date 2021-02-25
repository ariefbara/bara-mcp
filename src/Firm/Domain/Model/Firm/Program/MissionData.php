<?php

namespace Firm\Domain\Model\Firm\Program;

class MissionData
{

    /**
     * 
     * @var string|null
     */
    protected $name;

    /**
     * 
     * @var string|null
     */
    protected $description;

    /**
     * 
     * @var string|null
     */
    protected $position;

    public function __construct(?string $name, ?string $description, ?string $position)
    {
        $this->name = $name;
        $this->description = $description;
        $this->position = $position;
    }

    public function getName(): ?string
    {
        return $this->name;
    }

    public function getDescription(): ?string
    {
        return $this->description;
    }

    public function getPosition(): ?string
    {
        return $this->position;
    }

}
