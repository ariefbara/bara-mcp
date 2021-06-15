<?php

namespace Query\Domain\Model\Shared\Form;

use Query\Domain\Model\Shared\ {
    Form,
    Form\SelectField\Option
};

class SingleSelectField
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
     * @var SelectField
     */
    protected $selectField;

    /**
     *
     * @var string||null
     */
    protected $defaultValue = null;

    /**
     *
     * @var bool
     */
    protected $removed = false;

    function getForm(): Form
    {
        return $this->form;
    }

    function getId(): string
    {
        return $this->id;
    }

    function getDefaultValue(): ?string
    {
        return $this->defaultValue;
    }

    function isRemoved(): bool
    {
        return $this->removed;
    }

    protected function __construct()
    {
        ;
    }

    function getName(): string
    {
        return $this->selectField->getName();
    }

    function getDescription(): ?string
    {
        return $this->selectField->getDescription();
    }

    function getPosition(): ?string
    {
        return $this->selectField->getPosition();
    }

    function isMandatory(): bool
    {
        return $this->selectField->isMandatory();
    }

    /**
     * 
     * @return Option[]
     */
    function getUnremovedOptions()
    {
        return $this->selectField->getUnremovedOptions();
    }
    
    public function idEquals(string $id): bool
    {
        return $this->id === $id;
    }

}
