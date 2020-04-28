<?php

namespace Shared\Domain\Model\Form\SelectField;

use Shared\Domain\Model\Form\SelectField;

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
    protected $removed;

    function getId(): string
    {
        return $this->id;
    }

    function getName(): string
    {
        return $this->name;
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
