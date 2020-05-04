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

    function getId(): string
    {
        return $this->id;
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

}
