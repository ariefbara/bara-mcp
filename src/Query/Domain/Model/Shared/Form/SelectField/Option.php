<?php

namespace Query\Domain\Model\Shared\Form\SelectField;

use Query\Domain\Model\Shared\Form\SelectField;

class Option
{

    /**
     *
     * @var SelectField
     */
    protected $selectField;

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
    protected $removed = false;

    function getSelectField(): SelectField
    {
        return $this->selectField;
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

    function isRemoved(): bool
    {
        return $this->removed;
    }

    protected function __construct()
    {
        ;
    }

}
