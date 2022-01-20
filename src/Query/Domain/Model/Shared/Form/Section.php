<?php

namespace Query\Domain\Model\Shared\Form;

use Query\Domain\Model\Shared\Form;

class Section
{

    /**
     * 
     * @var Form
     */
    protected $form;

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
    protected $position;

    /**
     * 
     * @var bool
     */
    protected $removed;

    public function getForm(): Form
    {
        return $this->form;
    }

    public function getId(): string
    {
        return $this->id;
    }

    public function getName(): string
    {
        return $this->name;
    }

    public function getPosition(): ?string
    {
        return $this->position;
    }

    public function isRemoved(): bool
    {
        return $this->removed;
    }

    public function __construct()
    {
        
    }

}
