<?php

namespace Query\Domain\Model\Shared\Form;

class FieldVO
{

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
    protected $mandatory;

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

    function isMandatory(): bool
    {
        return $this->mandatory;
    }

    protected function __construct()
    {
        ;
    }

}
