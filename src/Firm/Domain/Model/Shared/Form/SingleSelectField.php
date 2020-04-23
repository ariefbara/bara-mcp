<?php

namespace Firm\Domain\Model\Shared\Form;

use Firm\Domain\Model\Shared\ {
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
     * @var string
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

    function __construct(Form $form, string $id, SingleSelectFieldData $singleSelectFieldData)
    {
        $this->form = $form;
        $this->id = $id;
        $this->selectField = new SelectField($id, $singleSelectFieldData->getSelectFieldData());
        $this->defaultValue = $singleSelectFieldData->getDefaultValue();
        $this->removed = false;
    }

    public function update(SingleSelectFieldData $singleSelectFieldData): void
    {
        $this->defaultValue = $singleSelectFieldData->getDefaultValue();
        $this->selectField->update($singleSelectFieldData->getSelectFieldData());
    }

    public function remove(): void
    {
        $this->removed = true;
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

}
