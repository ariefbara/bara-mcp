<?php

namespace Firm\Domain\Model\Shared\Form;

class SectionData
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
    protected $position;

    public function getName(): ?string
    {
        return $this->name;
    }

    public function getPosition(): ?string
    {
        return $this->position;
    }

    public function __construct(?string $name, ?string $position)
    {
        $this->name = $name;
        $this->position = $position;
    }

}
