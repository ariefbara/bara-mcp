<?php

namespace Firm\Domain\Model\Shared\Form;

use Firm\Domain\Model\Shared\{
    Form,
    Form\SelectField\Option
};
use Resources\Domain\ValueObject\IntegerRange;

class MultiSelectField
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
     * @var IntegerRange
     */
    protected $minMaxValue;

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

    function isRemoved(): bool
    {
        return $this->removed;
    }

    function __construct(Form $form, string $id, MultiSelectFieldData $multiSelectFieldData)
    {
        $this->form = $form;
        $this->id = $id;
        $this->selectField = new SelectField($id, $multiSelectFieldData->getSelectFieldData());
        $this->minMaxValue = new IntegerRange($multiSelectFieldData->getMinValue(), $multiSelectFieldData->getMaxValue());
        $this->removed = false;
    }

    public function update(MultiSelectFieldData $multiSelectFieldData): void
    {
        $this->minMaxValue = new IntegerRange($multiSelectFieldData->getMinValue(), $multiSelectFieldData->getMaxValue());
        $this->selectField->update($multiSelectFieldData->getSelectFieldData());
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

    function getMinValue(): ?int
    {
        return $this->minMaxValue->getMinValue();
    }

    function getMaxValue(): ?int
    {
        return $this->minMaxValue->getMaxValue();
    }

}
