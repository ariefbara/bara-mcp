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

}
